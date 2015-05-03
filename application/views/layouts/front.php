<!DOCTYPE html>
<html lang="<?php if($id_lang == 1) echo 'fr'; elseif($id_lang == 2) echo 'de' ?>">
	<head>
	<?php if(!empty($meta['title'])): ?><title><?php echo $meta['title'] ?></title><?php endif; ?>
    
    <?php if(!empty($meta['description'])): ?><meta name="description" content="<?php echo $meta['description'] ?>"><?php endif; ?>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" CONTENT="noindex, nofollow">

    <!-- Bootstrap -->
    <link href="<?php echo sbase_url() ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
    <!-- App specific -->
    <link href="<?php echo sbase_url() ?>assets/css/styles.css" rel="stylesheet" media="all">
    
    <!-- Controller output - CSS -->
    <?php echo output_css(); ?>
    <!-- /Controller output - CSS -->
    <script>
        var base_url = <?php echo '"'.  sbase_url().'"' ?>;
        var csrf_token_name = <?php echo '"'.$this->security->get_csrf_token_name().'"' ?>;
        var csrf_token_hash = <?php echo '"'.$this->security->get_csrf_hash().'"' ?>;
    </script>    

	</head>
<body <?php if(isset($page_name)): ?>id="<?php echo $page_name ?>"<?php endif; ?> >
    
<!-- container -->
<div class="container">

    <div class="row">
        <div class="col-md-8"><img src="<?php echo sbase_url() ?>assets/img/logo.jpg" height="100"></div>
        <?php
            if(!isset($dont_show_language_switch_on_header)):
        ?>
            <div class="col-md-4"><a href="<?php echo $uri ?>?lang=fr">Français</a> | <a href="<?php echo $uri ?>?lang=de">Deutsch</a></div>
        <?php
            endif;
        ?>
    </div>
                                              


<!-- Views -->
<?php echo $output ?>
<!-- /Views -->    

<div class="clearfix"><br /></div>

<!-- Footer -->
<div class="footer">
    <div class="row">
        <div class="col-md-12">
            Copyright 2015 | Buderus | Created by : Kumkuat | All rights reserved
        </div>
    </div>
</div>
<!-- /Footer -->



</div>
<!-- /container -->

    <script src="<?php echo sbase_url() ?>assets/jquery/jquery-1.11.0.min.js"></script>
    <script src="<?php echo sbase_url() ?>assets/bootstrap/js/bootstrap.min.js"></script>    
    
    <!-- Controller output - JS -->
    <?php echo output_js(); ?>
    <!-- /Controller output - JS -->
    
    <script type="text/javascript">  
        //$('body').on('touchstart.dropdown','.dropdown-menu',function(e) {e.stopPropagation();});
        $(document).ready(function () {  
            $('.dropdown-toggle').dropdown();  
        });  
    </script> 

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-62396618-1', 'auto');
      ga('send', 'pageview');

    </script>
</body>
</html>