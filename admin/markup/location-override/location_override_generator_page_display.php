<script>
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    var _nonce='<?php echo wp_create_nonce('ifso-render-preview'); ?>';
    var all_countries_opts = <?php
    include_once(IFSO_PLUGIN_BASE_DIR. 'public/models/data-rules/country_opts_variable.php');
    echo isset($country_opts) ? json_encode($country_opts) : '';
    ?>
</script>


<?php if(!defined('IFSO_ADD_TO_GROUP_FORM_ON') || !IFSO_ADD_TO_GROUP_FORM_ON){ ?>
    <div class="compat-notice yellow-noticebox">The location override functionality requires the Audience Self-selection Extension in order to work. <a href="https://www.if-so.com/dynamic-select-form??utm_source=Plugin&utm_medium=GeoPage&utm_campaign=GeoOverrideTool" target="_blank">Free download.</a></div>
<?php }?>

<?php include 'preview-design.php'; ?>

<div class="generator-content">
    <div class="generator-menu">
        <?php include 'generator-form.php';?>
    </div>
    <div class="generator-sidebar">
        <?php include 'generator-results.php';?>
    </div>
</div>