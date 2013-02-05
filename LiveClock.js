function refrClock() {
	var docId = document.getElementById("clock");
	if(!docId) {
		setTimeout(function() {
			refrClock();
		},1000);
		return;
	}

	var offset = Math.abs(new Date().getTimezoneOffset()/60);
	//console.log(offset)
	d = new Date();
	utc = d.getTime() + (d.getTimezoneOffset() * 60000);
	nd = new Date(utc + (3600000*+ offset));
	var s=nd.getSeconds();
	var m=nd.getMinutes();
	var h=nd.getHours();
	var am_pm;
	if (s<10) {s="0" + s}
	if (m<10) {m="0" + m}
	if (h>12) {h-=12;am_pm = "pm"}
	else {am_pm="am"}
	if (h<10) {h="0" + h}
	docId.innerHTML=h + ":" + m + ":" + s + am_pm;
	setTimeout("refrClock()",1000);
}
refrClock();


/*
function refrClock()
{
d = new Date();
utc = d.getTime() + (d.getTimezoneOffset() * 60000);
nd = new Date(utc + (3600000*+2));
var s=nd.getSeconds();
var m=nd.getMinutes();
var h=nd.getHours();
var am_pm;
if (s<10) {s="0" + s}
if (m<10) {m="0" + m}
if (h>12) {h-=12;am_pm = "pm"}
else {am_pm="am"}
if (h<10) {h="0" + h}
document.getElementById("clock").innerHTML=h + ":" + m + ":" + s + am_pm;
setTimeout("refrClock()",1000);
}
refrClock();*/