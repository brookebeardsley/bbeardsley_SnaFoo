<!doctype html>
<html class="no-js" lang="en-us">
<head>
    <!-- META DATA -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--[if IE]><meta http-equiv="cleartype" content="on" /><![endif]-->
    <!-- SEO -->
    <title>SnaFoo - Nerdery Snack Food Ordering System</title>
    <!-- ICONS -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-57x57.png');?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-60x60.png');?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-72x72.png');?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-76x76.png');?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-114x114.png');?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-120x120.png');?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-144x144.png');?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-152x152.png');?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/media/images/favicon/apple-touch-icon-180x180.png');?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo base_url('assets/media/images/favicon/favicon-192x192.png');?>">
    <link rel="icon" type="image/png" sizes="160x160" href="<?php echo base_url('assets/media/images/favicon/favicon-160x160.png');?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url('assets/media/images/favicon/favicon-96x96.png');?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/media/images/favicon/favicon-32x32.png');?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/media/images/favicon/favicon-16x16.png');?>">
    <meta name="msapplication-TileImage" content="<?php echo base_url('assets/media/images/favicon/mstile-144x144.png');?>">
    <meta name="msapplication-TileColor" content="#ff0000">
    <!-- STYLESHEETS -->
    <link rel="stylesheet" media="screen, projection" href="<?php echo base_url('assets/styles/modern.css');?>" />
    <link rel="stylesheet" media="screen, projection" href="<?php echo base_url('assets/styles/app.css');?>" />
    <script src="<?php echo base_url('assets/js/angular.min.js');?>"></script>
</head>
<body>
    <div class="masthead" role="banner">
        <div class="masthead-hd">
            <h1 class="hdg hdg_1 mix-hdg_extraBold"><a href="<?php echo base_url();?>">SnaFoo</a></h1>
            <p class="masthead-hd-sub">Nerdery Snack Food Ordering System</p>
        </div>
        <div class="masthead-nav" role="navigation">
            <ul>
                <li><a href="<?php echo base_url();?>">Voting</a></li>
<?php if(!$has_made_a_suggestion):?>
                <li><a href="<?php echo base_url('Snack/suggestions');?>">Suggestions</a></li>
<?php else:?>
                <li><a href="<?php echo base_url('Snack/suggestions');?>" style="text-decoration: line-through !important; color:#AAA;">Suggestions</a></li>
<?php endif;?>
                <li><a href="<?php echo base_url('Snack/shopping');?>">Shopping List</a></li>
            </ul>
        </div>
    </div>
<?php if(isset($error_message)):?>
<div class="wrapper">
  <div class="shelf shelf_2">
    <p class="error"><?php echo $error_message;?></p>
  </div>
</div>
<?php endif;?>
