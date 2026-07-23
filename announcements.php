<!DOCTYPE html>
<html>
<title>W3.CSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<style>
.mySlides {display:none;}
</style>
<body>
 
<div class="w3-content" style="max-width:100%; max-height:100%; margin: 5px 5px 5px 5px;">

  <div style="padding: 3px;" class="mySlides w3-container w3-xlarge w3-white w3-card-4">
    <img src="img/morping-spinner.gif" style="max-width:40%; max-height:40%">
    <p style="max-width:20%; max-height:20%"><span class="w3-tag w3-yellow">New!</span>
    <p style="max-width:40%; max-height:40%">1 Crystal Glasses</p>
  </div>
  
  <div style="padding: 3px;" class="mySlides w3-container w3-xlarge w3-white w3-card-4">
    <img src="img/morping-spinner.gif" style="max-width:40%; max-height:40%">
    <p style="max-width:20%; max-height:20%"><span class="w3-tag w3-yellow">New!</span>
    <p style="max-width:40%; max-height:40%">2 Crystal Glasses</p>
  </div>
  
  
  <div style="padding: 3px;" class="mySlides w3-container w3-xlarge w3-white w3-card-4">
    <img src="img/morping-spinner.gif" style="max-width:40%; max-height:40%">
    <p style="max-width:20%; max-height:20%"><span class="w3-tag w3-yellow">New!</span>
    <p style="max-width:40%; max-height:40%">3 Crystal Glasses</p>
  </div>
  
 
</div>

<script>
var slideIndex = 0;
carousel();

function carousel() {
  var i;
  var x = document.getElementsByClassName("mySlides");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none"; 
  }
  slideIndex++;
  if (slideIndex > x.length) {slideIndex = 1} 
  x[slideIndex-1].style.display = "block"; 
  setTimeout(carousel, 15000); 
}
</script>

</body>
</html>
