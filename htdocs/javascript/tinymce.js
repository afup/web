 $().ready(function() {
    $('textarea.tinymce').tinymce({
      // Location of TinyMCE script
      script_url : '../../javascript/tiny_mce/tiny_mce.js',

      // General options
      theme : "advanced",
      plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

      // Theme options
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,code",
      theme_advanced_buttons2 : null,
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,

      // Example content CSS (should be your site CSS)
      content_css : "style.css",

    });
  });