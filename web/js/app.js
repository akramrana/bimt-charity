/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var app = {
    changeStatus: function (url, trigger, id, force_reload)
    {
        var status = 0;
        if ($('#' + trigger.id).is(":checked")) {
            status = 1;
        }
        $('.global-loader').show();
        $.ajax({
            type: "GET",
            url: baseUrl + url,
            data: {
                "id": id
            },
            success: function (res) {
                $(".global-loader").hide();
                if (force_reload)
                {
                    location.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                console.log(jqXHR.responseText);
            }
        });
    },
    showHideMonthlyInvoice:function()
    {
        if($("#paymentreceived-has_invoice").is(":checked")){
            $("#monthly-invoice").removeClass("hidden");
            $("#instalment-month-year").addClass("hidden");
            $("#paymentreceived-amount").val("0");
        }else{
            $("#monthly-invoice").addClass("hidden");
            $("#instalment-month-year").removeClass("hidden");
            $("#paymentreceived-amount").val("");
        }
    }
};

