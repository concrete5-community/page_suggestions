<?php 
defined('C5_EXECUTE') or die("Access Denied.");

if (!$debug && !$c->isEditMode() && (count($keywords) === 0 OR count($results) === 0)) {
    return false;
}
?>

<div class="page-suggestions">
    <?php 
    if (($c->isEditMode() && !empty($block_title)) || (count($results) > 0 || ($debug && count($keywords > 0)))) {
        echo '<h2>'.$block_title.'</h2>';
    }

    if ($debug && !$c->isEditMode()) {
        echo '<pre>';
        echo t("All keywords:");
        var_dump($all_keywords);

        echo t("Filtered keywords:");
        var_dump($filtered_keywords);

        echo t("Sorted keywords:");
        var_dump($sorted_keywords);
        echo '</pre>';
    } elseif ($c->isEditMode()) {
        echo t("Page Suggestions is disabled in edit mode.");
    }

    if (count($results) > 0) {
        ?>
        <div class="pl-results">
            <?php 
            foreach ($results as $page) {
                ?>
                <div class="result">
                    <h3><a href="<?php echo  $page->getCollectionLink() ?>"><?php echo  $page->getCollectionName() ?></a></h3>
                    <p>
                        <?php 
                        if ($page->getCollectionDescription()) {
                            echo Core::make('helper/text')->shorten($page->getCollectionDescription());
                            echo '<br />';
                        }
                        echo Core::make('helper/text')->shorten($page->getPageIndexContent(), 180);
                        ?> <a href="<?php echo  $page->getCollectionLink() ?>" class="page-link"><?php echo  $page->getCollectionLink() ?></a>
                    </p>
                </div>
                <?php 
            }
            ?>
        </div>
        <?php 
    }
    ?>
</div>
