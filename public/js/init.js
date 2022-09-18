function saveStorage($key,$value){
	localStorage.setItem($key, $value);
}

function getStorage($key){
	return localStorage.getItem($key) ? localStorage.getItem($key) : false;
}

function resetStorage(){
	localStorage.clear();
}
