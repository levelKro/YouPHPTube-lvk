<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="The YouPHPTube">
<meta name="author" content="Daniel Neto">
<link rel="icon" href="<?php echo $global['webSiteRootURL']; ?>img/favicon.png">
<link href="<?php echo $global['webSiteRootURL']; ?>bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>js/seetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>bootstrap/bootstrapSelectPicker/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>css/flag-icon-css-master/css/flag-icon.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>js/bootgrid/jquery.bootgrid.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>css/custom/<?php echo $config->getTheme(); ?>.css" rel="stylesheet" type="text/css" id="theme"/>
<link href="<?php echo $global['webSiteRootURL']; ?>css/main.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>css/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $global['webSiteRootURL']; ?>js/bootstrap-toggle/bootstrap-toggle.min.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo $global['webSiteRootURL']; ?>js/jquery-3.2.0.min.js" type="text/javascript"></script>
<script>
    var webSiteRootURL = '<?php echo $global['webSiteRootURL']; ?>';
</script> 
<?php
if(!$config->getDisable_analytics()){
	/* Removed the Google Analytics from YouPHPTube */
?>
 
<?php
}
echo $config->getHead();
?>