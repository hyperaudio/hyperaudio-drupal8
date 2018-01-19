window.onload = function() {
  // minimizedMode is still experimental
  var minimizedMode = true;
  
  hyperaudiolite.init("#hypertranscript", "#hyperplayer", minimizedMode);
  
  if (ShareThis) {
     ShareThis({
       sharers: [ ShareThisViaTwitter, ShareThisViaFacebook ],
       selector: "#hypertranscript"
     }).init();
  }
}
