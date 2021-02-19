<?php 
namespace Concrete\Package\PageSuggestions\Block\PageSuggestions;

use Core;
use Package;
use Page;
use PageList;
use Request;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{

    protected $btTable = 'btPageSuggestions';
    protected $btDefaultSet = 'navigation';
    protected $btInterfaceWidth = '600';
    protected $btInterfaceHeight = '465';

    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = false;

    protected $helpers = array('form', 'security');
    protected $iMaxKeywordsDefault = 4;
    protected $iMaxPagesDefault = 4;
    protected $iMinWordLengthDefault = 1;

    public function getBlockTypeName()
    {
        $pkg = Package::getByHandle('page_suggestions');
        return $pkg->getPackageName();
    }


    public function getBlockTypeDescription()
    {
        $pkg = Package::getByHandle('page_suggestions');
        return $pkg->getPackageDescription();
    }


    public function add()
    {
        $this->set('excluded_words', 'submit, page_not_found, ccm_token, cID, ctask, query, submit, html, pages, page, index');
        $this->set('max_keywords_default', $this->iMaxKeywordsDefault);
        $this->set('max_pages_default', $this->iMaxPagesDefault);
        $this->set('min_word_length_default', $this->iMinWordLengthDefault);
    }


    public function edit()
    {
        $this->set('max_keywords_default', $this->iMaxKeywordsDefault);
        $this->set('max_pages_default', $this->iMaxPagesDefault);
        $this->set('min_word_length_default', $this->iMinWordLengthDefault);
    }


    public function save($args)
    {
        // C5 doesn't allow us to save NULL values, unfortunately...
        $args['max_keywords']    = (empty($args['max_keywords'])) ? $this->iMaxKeywordsDefault : intval($args['max_keywords']);
        $args['max_pages']       = (empty($args['max_pages'])) ? $this->iMaxPagesDefault : intval($args['max_pages']);
        $args['min_word_length'] = (empty($args['min_word_length'])) ? $this->iMinWordLengthDefault : intval($args['min_word_length']);

        parent::save($args);
    }


    /**
     * Render the block.
     *
     * Several variables are set for debugging purposes.
     */
    public function view()
    {
        $c = Page::getCurrentPage();

        if (!$c or $c->isError()) {
            return false;
        }

        $this->set('c', $c);
        
        $app = Core::getFacadeApplication();
        $this->setApplication($app);

        $all_keywords = $this->getAllKeywords();
        $filtered_keywords = $this->filterKeywords($all_keywords);
        $sorted_keywords = $this->sortKeywords($filtered_keywords);


        $keywords = $sorted_keywords;
        if (count($keywords) > $this->max_keywords) {
            $keywords = array_slice($keywords, 0, $this->max_keywords);
        }

        if (count($keywords) && !$c->isEditMode()) {
            $this->executeSearch($keywords, $c->getCollectionID());
        }

        $this->set('all_keywords', $all_keywords);
        $this->set('filtered_keywords', $filtered_keywords);
        $this->set('sorted_keywords', $sorted_keywords);
        $this->set('keywords', $keywords);
        $this->set('block_title', trim($this->block_title));
    }


    /**
     * Sorts keywords descending and alphabetically.
     *
     * @param array $keywords
     * @return array
     */
    public function sortKeywords($keywords)
    {
        usort($keywords, function($a, $b) {
            $diff = strlen($b) - strlen($a);
            return $diff ?: strcmp($a, $b);
        });

        return $keywords;
    }


    /**
     * Filter keywords.
     *
     * - Remove duplicates
     * - Remove excluded words
     * - Remove words shorter than x characters
     *
     * @param array $keywords
     * @return array
     */
    protected function filterKeywords($keywords)
    {
        // Convert comma separated string to an array
        $excluded_words = explode(',', $this->excluded_words);

        // Remove spaces from excluded keywords
        $excluded_words = array_map('trim', $excluded_words);

        $filtered = array();

        // Remove duplicate keywords
        $keywords = array_unique($keywords);

        foreach ($keywords as $index => $keyword) {
            if (strlen($keyword) < $this->min_word_length) {
                continue;
            }

            if (in_array($keyword, $excluded_words)) {
                continue;
            }

            $filtered[] = $keyword;
        }

        return $filtered;
    }


    /**
     * Get all keywords from the current URL.
     * Replace characters like ?/. with spaces.
     *
     * @return array
     */
    protected function getAllKeywords()
    {
        $keywords = array();

        $request_uri = $this->getRequestURI();

        // Replace characters with space
        $request_uri = str_replace(array('/', '-', '%20', '.', '=', '?', ':', '&'), ' ', $request_uri);
        foreach(explode(' ', $request_uri) as $keyword) {
            if (strlen($keyword) > 0) {
                $keyword = trim($keyword);
                $keywords[] =  $this->app->make('helper/security')->sanitizeString($keyword);
            }
        }

        return $keywords;
    }


    /**
     * Get path info and query string.
     *
     * Path info: e.g. subfolder (/subdir).
     * Query string: e.g. 'q=query&locale=de_DE'.
     *
     * @return string (/some/page?with=params)
     */
    protected function getRequestURI()
    {
        $req = Request::getInstance();

        return $req->getPathInfo().'?'.$req->getQueryString();
    }


    /**
     * Perform a search based on given keywords.
     * Filter fulltext. When multiple keywords are given, use OR.
     *
     * If you'd like to customize the results, you probably want to edit or overrule this function.
     *
     * @param array $keywords
     * @param int $cID current page ID
     * @return void
     */
    protected function executeSearch($keywords, $cID)
    {
        $pl = new PageList();
        $pl->filterByFulltextKeywords(implode(" ", $keywords));

        // Exclude current page
        $pl->getQueryObject()->where('p.cID != :cID')->setParameter('cID', $cID);

        $pagination = $pl->getPagination();
        $pagination->setMaxPerPage($this->max_pages);
        $results = $pagination->getCurrentPageResults();

        $this->set('page_list', $pl);
        $this->set('pagination', $pagination);
        $this->set('results', $results);
    }
}