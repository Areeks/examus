var currentDate = new Date();
	var countDownDate = new Date(currentDate.getTime() + 60*60000);
var myfunc = setInterval(function() {

    var now = new Date().getTime();
    var timeleft = countDownDate - now;
     
    var minutes = Math.floor((timeleft % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((timeleft % (1000 * 60)) / 1000);
   
    document.getElementById("mins").innerHTML = minutes; 
    document.getElementById("secs").innerHTML = seconds;
        
    // Display the message when countdown is over
    if (timeleft < 0) {
        clearInterval(myfunc);
        document.getElementById("mins").innerHTML = "";
        document.getElementById("secs").innerHTML = "";
		document.getElementById("dwukropek").innerHTML = "";
        document.getElementById("end").innerHTML = "Wylogowano!";
    }
    }, 1000);
function isLogout()
{
alert("Upłynął limit czasu aktywności. Zostaniesz wylogowany!");
window.location.replace('logout.php');
}
function isQuestion(id)
{
alert("Pytyanie istnieje w bazie! - Zostaniesz przeniesiony!");
window.location.replace('index.php#catid'+id);
}