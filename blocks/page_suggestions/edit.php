<?php      
defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<div class="form-group">
    <label class="control-label" for="block_title">
        <?php  echo t("Block title"); ?>
        <i class="fa fa-question-circle launch-tooltip"
            title="<?php  echo t("Leave empty if you don't want a title above the results. The title will only be visible if there are page suggestions."); ?>"></i>
    </label>
    <div class="input">
        <?php 
        echo $form->text('block_title', $block_title);
        ?>
    </div>
</div>

<div class="form-group">
    <label class="control-label" for="excluded_words">
        <?php  echo t("Excluded words"); ?>
        <i class="fa fa-question-circle launch-tooltip"
           title="<?php  echo t("Comma separated list of words that should be excluded as search keywords."); ?>"></i>
    </label>
    <div class="input">
        <?php 
        echo $form->textarea('excluded_words', $excluded_words);
        ?>
    </div>
</div>

<div class="form-group">
    <label class="control-label" for="min_word_length">
        <?php  echo t("Minimal word length"); ?>
        <i class="fa fa-question-circle launch-tooltip"
           title="<?php  echo t("Instead of entering a whole list of tiny words that should be excluded, you can also define a minimal character length for keywords."); ?>"></i>
    </label>
    <div class="input">
        <?php 
        if ($min_word_length == $min_word_length_default) {
            unset($min_word_length);
        }
        echo $form->number('min_word_length', $min_word_length, array('placeholder' => t("%d (default)", $min_word_length_default), 'min' => 0));
        ?>
    </div>
</div>

<div class="form-group">
    <label class="control-label" for="max_keywords">
        <?php  echo t("Limit number of keywords from URL"); ?>
        <i class="fa fa-question-circle launch-tooltip"
           title="<?php  echo t("If a long URL results in a 404-page, you probably have a lot of keywords. You should limit the number of keywords to get better page suggestions. The block automatically sorts the keywords from the URL by length."); ?>"></i>
    </label>
    <div class="input">
        <?php 
        if ($max_keywords == $max_keywords_default) {
            unset($max_keywords);
        }
        echo $form->number('max_keywords', $max_keywords, array('placeholder' => t("%d (default)", $max_keywords_default), 'min' => 1));
        ?>
    </div>
</div>

<div class="form-group">
    <label class="control-label" for="debug">
        <?php  echo t("Debug mode"); ?>
        <i class="fa fa-question-circle launch-tooltip"
           title="<?php  echo t("If you don't get any results or you want to know what the block is doing, you can enable the debug mode. This is for testing purposes only."); ?>"></i>
    </label>

    <div class="input">
        <?php 
        echo $form->select('debug', array(0 => t('Disabled (default)'), 1 => t('Enabled')), $debug);
        ?>
    </div>
</div>