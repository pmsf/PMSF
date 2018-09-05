<?php

require_once("config/config.php");
?>
<html>
<style>
body {
  padding: 0;
  margin: 0;
  background: repeating-linear-gradient(45deg, darkred, darkred 2px, #330000 3px, #330000 8px);
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}
#spam {
  background-color: lightgray;
  border-radius: 5px;
  margin: auto;
  width: 85%;
  cursor: not-allowed;
}
h1 {
  padding-top: 30px;
  font-size: 60px;
  font-family: Arial;
  text-align: center;
  border-bottom: 1px solid #333;
  border-bottom-style: dashed;
  padding-bottom: 20px;
}
spam {
   color: red;
   text-decoration: none;
   cursor: not-allowed;
}
p {
  text-align: center;
  font-family: Arial;
  font-weight: bold;
  padding-bottom: 3%;
}
small {
  font-family: Arial;
  text-align: center;
  word-spacing: -1px;
  font-size: 10px;
  font-weight: normal;
  color: gray;
}
#gears {
  text-align: center;
  color: #333;
  width: 200px;
  margin: auto;
  border-radius: 10px;
  background-color: rgba(0,0,0,0.85);
  /* opacity: 0.85; */
  padding: 30px 0px;
}
</style>
<body>
  <div id="spam">
  <h1><spam>X</spam> Access Denied(403)</h1>
    <?php echo '<p>As far as we can see you didn\'t join our Discord server. <a href="' . $discordUrl . '" target="_blank" style="margin-bottom: 5px; vertical-align: middle;padding:0 5px;">
	    <img src="https://discordapp.com/assets/fc0b01fe10a0b8c602fb0106d8189d9b.png" border="0" height="30" width="auto" style="margin-left: 10px;"></a><br/><br/>It might also be possible that you joined a server which is on our blacklist<br/>'; ?>
    <small>---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</small></p>
  </div><br><br>
  <div id="gears">
<svg xmlns="http://www.w3.org/2000/svg" width="120px" height="120px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-gears"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"/><g transform="translate(-20,-20)"><path d="M79.9,52.6C80,51.8,80,50.9,80,50s0-1.8-0.1-2.6l-5.1-0.4c-0.3-2.4-0.9-4.6-1.8-6.7l4.2-2.9c-0.7-1.6-1.6-3.1-2.6-4.5 L70,35c-1.4-1.9-3.1-3.5-4.9-4.9l2.2-4.6c-1.4-1-2.9-1.9-4.5-2.6L59.8,27c-2.1-0.9-4.4-1.5-6.7-1.8l-0.4-5.1C51.8,20,50.9,20,50,20 s-1.8,0-2.6,0.1l-0.4,5.1c-2.4,0.3-4.6,0.9-6.7,1.8l-2.9-4.1c-1.6,0.7-3.1,1.6-4.5,2.6l2.1,4.6c-1.9,1.4-3.5,3.1-5,4.9l-4.5-2.1 c-1,1.4-1.9,2.9-2.6,4.5l4.1,2.9c-0.9,2.1-1.5,4.4-1.8,6.8l-5,0.4C20,48.2,20,49.1,20,50s0,1.8,0.1,2.6l5,0.4 c0.3,2.4,0.9,4.7,1.8,6.8l-4.1,2.9c0.7,1.6,1.6,3.1,2.6,4.5l4.5-2.1c1.4,1.9,3.1,3.5,5,4.9l-2.1,4.6c1.4,1,2.9,1.9,4.5,2.6l2.9-4.1 c2.1,0.9,4.4,1.5,6.7,1.8l0.4,5.1C48.2,80,49.1,80,50,80s1.8,0,2.6-0.1l0.4-5.1c2.3-0.3,4.6-0.9,6.7-1.8l2.9,4.2 c1.6-0.7,3.1-1.6,4.5-2.6L65,69.9c1.9-1.4,3.5-3,4.9-4.9l4.6,2.2c1-1.4,1.9-2.9,2.6-4.5L73,59.8c0.9-2.1,1.5-4.4,1.8-6.7L79.9,52.6 z M50,65c-8.3,0-15-6.7-15-15c0-8.3,6.7-15,15-15s15,6.7,15,15C65,58.3,58.3,65,50,65z" fill="#8f7f59" transform="rotate(32.43 50 50)"><animateTransform attributeName="transform" type="rotate" from="90 50 50" to="0 50 50" dur="1s" repeatCount="indefinite"/></path></g><g transform="translate(20,20) rotate(15 50 50)"><path d="M79.9,52.6C80,51.8,80,50.9,80,50s0-1.8-0.1-2.6l-5.1-0.4c-0.3-2.4-0.9-4.6-1.8-6.7l4.2-2.9c-0.7-1.6-1.6-3.1-2.6-4.5 L70,35c-1.4-1.9-3.1-3.5-4.9-4.9l2.2-4.6c-1.4-1-2.9-1.9-4.5-2.6L59.8,27c-2.1-0.9-4.4-1.5-6.7-1.8l-0.4-5.1C51.8,20,50.9,20,50,20 s-1.8,0-2.6,0.1l-0.4,5.1c-2.4,0.3-4.6,0.9-6.7,1.8l-2.9-4.1c-1.6,0.7-3.1,1.6-4.5,2.6l2.1,4.6c-1.9,1.4-3.5,3.1-5,4.9l-4.5-2.1 c-1,1.4-1.9,2.9-2.6,4.5l4.1,2.9c-0.9,2.1-1.5,4.4-1.8,6.8l-5,0.4C20,48.2,20,49.1,20,50s0,1.8,0.1,2.6l5,0.4 c0.3,2.4,0.9,4.7,1.8,6.8l-4.1,2.9c0.7,1.6,1.6,3.1,2.6,4.5l4.5-2.1c1.4,1.9,3.1,3.5,5,4.9l-2.1,4.6c1.4,1,2.9,1.9,4.5,2.6l2.9-4.1 c2.1,0.9,4.4,1.5,6.7,1.8l0.4,5.1C48.2,80,49.1,80,50,80s1.8,0,2.6-0.1l0.4-5.1c2.3-0.3,4.6-0.9,6.7-1.8l2.9,4.2 c1.6-0.7,3.1-1.6,4.5-2.6L65,69.9c1.9-1.4,3.5-3,4.9-4.9l4.6,2.2c1-1.4,1.9-2.9,2.6-4.5L73,59.8c0.9-2.1,1.5-4.4,1.8-6.7L79.9,52.6 z M50,65c-8.3,0-15-6.7-15-15c0-8.3,6.7-15,15-15s15,6.7,15,15C65,58.3,58.3,65,50,65z" fill="#9f9fab" transform="rotate(57.57 50 50)"><animateTransform attributeName="transform" type="rotate" from="0 50 50" to="90 50 50" dur="1s" repeatCount="indefinite"/></path></g></svg></div>
</body>
</html>
