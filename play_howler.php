
<h3 onclick="PopupCenterValid()">Welcome to howler!</h3>
<script src="js/howler.min.js"></script>


<script>
 function PopupCenterValid(){
    var sound = new Howl({
      src: ['RFID_FX/gate_access.mp3'],
      volume: 1,
    });
    sound.play()
}
</script>