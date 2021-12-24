/** 
* \brief 
**/

function sendMyForm(){
    event.preventDefault();

    //Add span to print request status
    if (document.getElementById("label-status-text") == null){
        document.querySelector("div.rsform-block-validation").insertAdjacentHTML('beforebegin', '<span id="label-status-text"></span>');
    }

    document.querySelector("#label-status-text").innerText = "Registration in progress...";
    document.querySelector("#label-status-text").classList.remove("success");
    document.querySelector("#label-status-text").classList.remove("fail");

    //AJAX Request
	fetch('https://www.geppia.com/test/script.php', {
		method: 'POST',
		body: '',
	}).then(function (response)
    {
		if (response.ok) {
			return response.json();
		}
		return Promise.reject(response);
	}).then(function (data)
    {
        // Response
        if (data['status'] == true){
            document.querySelector("#label-status-text").classList.add("success");
            document.querySelector("#label-status-text").classList.remove("fail");
        }
        else {
            document.querySelector("#label-status-text").classList.add("fail");
            document.querySelector("#label-status-text").classList.remove("success");
        }
        document.querySelector("#label-status-text").innerText = data['msg'];
	}).catch(function (error) 
    {
		alert(error);
	});

    return false;
}