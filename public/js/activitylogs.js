window.onload = () => {
    $.ajax({
        type: "GET",
        url: "/api/activitylogs/list",
        dataType: "json",
        success: res => {
            if (res.success) {
                $("#activityLogsTable").DataTable({
                    data: res.logs,
                    columns: [
                        { data: "name" },
                        { data: "action" },
                        { data: "result" },
                        { data: null,
                            render: data=> {
                                return formatDateTime(data.created_at);
                            }
                         },
                        { data: "device" },
                        {
                            data: null,
                            render: data => {
                                return `<button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#logDetails" onclick="viewLogDetails('${data.act_id}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 9v4" />
                                <path d="M12 16v.01" />
                                </svg> View More Info
                                </button>`
                            },
                        }
                    ],
                });
            }
        }, error: xhr => console.log(xhr.responseText)
    })
}


async function viewLogDetails(id){
    const response = await fetch(`/api/activitylogs/details/${id}`);
    const result = await response.json();

    setText('logName', result.logs.name);
    setText('logAction', result.logs.action);
    setText('logResult', result.logs.result);
    setText('logDevice', result.logs.device);
    setText('logTimestamp', formatDateTime(result.logs.created_at));

    setText('logApiCall', result.logs.api_calls);
    setText('logPageRoute', result.logs.page_route);
    setText('logRequestType', result.logs.request_type);
    setText('logSessionID', result.logs.session_id);
    setText('logSentData', JSON.stringify(result.logs.sent_data));
    setText('logResponseData', JSON.stringify(result.logs.response_data));
}
