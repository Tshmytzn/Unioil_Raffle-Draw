document.getElementById('authForm').addEventListener('submit', (e)=> {
    e.preventDefault();

    loading(true);

    $.ajax({
        type: "POST",
        url: "/api/admin/auth",
        data: $('#authForm').serialize(),
        success: res=> {
            loading(false);
            dataParser(res);

            if(res.success){

            }
        }, error: xhr=> console.log(xhr.responseText)
    })
});
