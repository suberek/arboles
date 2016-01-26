<script type="text/javascript">

window.history.pushState("ekisde","OMG LOL", "<?php echo $especie_URL ; ?>");

// Include the UserVoice JavaScript SDK (only needed once on a page)
UserVoice=window.UserVoice||[];
(function(){
  var uv=document.createElement('script');
  uv.type='text/javascript';
  uv.async=true;
  uv.src='//widget.uservoice.com/Eq8cib0TB3FmGnxB0NRmw.js';
  var s=document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(uv,s)
})();

UserVoice.push(['addTrigger', {
  mode: 'smartvote', // Modes: contact (default), smartvote, satisfaction
  trigger_position: 'top-right',
  trigger_color: 'white',
  trigger_background_color: '#5cba9d',
  accent_color: '#5cba9d',
  contact_enabled: false,
  trigger_style: 'icon',
  smartvote_title: '¿Con qué seguir?',
  menu_enabled : true
}]);


// GOOGLE ANALYTICS
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-678413-17', 'auto');
ga('require', 'displayfeatures');
ga('require', 'linkid', 'linkid.js');
ga('send', 'pageview');
<?php if (  !empty($busqueda)  ) {
// Seguimiento Conversiones (búsquedas)
?>
ga("send", "formulario posteado", "buscar", "arboles", <?php echo $total_registros_censo?> );

<?php 
}
?>
</script>