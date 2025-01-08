    <script>
     $(document).ready(function() {
      if (Array.prototype.forEach) {
             var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-primary'));
             elems.forEach(function(html) {
                 var switchery = new Switchery(html, { color: '#2196F3' });
             });
         }
         else {
             var elems = document.querySelectorAll('.switchery-primary');
             for (var i = 0; i < elems.length; i++) {
                 var switchery = new Switchery(elems[i], { color: '#2196F3' });
             }
         }
        });
      </script>