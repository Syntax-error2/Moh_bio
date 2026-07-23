<html>

  <head>
  
   
  </head>
  
  <body>
    <form>
      <input name="dept" type="text" /><br />
      <input name="gradeLevel" type="text" /><br />
      <input name="section" type="text" /><br />
      <input name="submit" type="submit" value="Submit">
    </form>
    
    
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    
    <script>
      $(function () {

        $('form').on('submit', function (e) {

          e.preventDefault();

          $.ajax({
            type: 'POST',
            url: 'login2.php',
            data: $('form').serialize(),
            success: function () {
              alert('form was submitted'); 
            }
          });

        });

      });
    </script>
 
  </body>
</html>