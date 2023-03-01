<script type="text/javascript">
  <?php if ((is_numeric($especie_id_busqueda)) && ($especie_id_busqueda > 0)) : ?>
    window.history.pushState("abolado","urbano", "<?php echo $especie_URL ; ?>");
  <?php elseif (isset($_POST['user_sabores'])) : ?>
    window.history.pushState("abolado","urbano", "sabores");
  <?php endif; ?>
</script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-CFQ9D8W1T3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-CFQ9D8W1T3');
</script>