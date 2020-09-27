<script type="text/javascript">
  <?php if ((is_numeric($especie_id_busqueda)) && ($especie_id_busqueda > 0)) : ?>
    window.history.pushState("abolado","urbano", "<?php echo $especie_URL ; ?>");
  <?php elseif (isset($_POST['user_sabores'])) : ?>
    window.history.pushState("abolado","urbano", "sabores");
  <?php endif; ?>

  // GOOGLE ANALYTICS
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-678413-17', 'auto');
  ga('require', 'displayfeatures');
  ga('require', 'linkid', 'linkid.js');
  ga('send', 'pageview');

  <?php if (!empty($busqueda)) : ?>
    ga("send", "formulario posteado", "buscar", "arboles", <?php echo $total_registros_censo?> );
  <?php endif; ?>
</script>
