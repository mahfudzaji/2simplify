function imageSlider(image){
    $(".image-scroll-modal").find(".modal-image").attr("src", image.img);
    $(".image-scroll-modal").find(".description").html(image.desc);

    if(image.next==0){
        $(".image-scroll-modal").find(".modal-nav-right").hide();
    }else{
        $(".image-scroll-modal").find(".modal-nav-right").show();
    }
    if(image.prev==0){
        $(".image-scroll-modal").find(".modal-nav-left").hide();
    }else{
         $(".image-scroll-modal").find(".modal-nav-left").show();
    }

    $(".image-scroll-modal").show();
}

function activeImage(currentImg){
    var nextCount=currentImg.next().length;
    var prevCount=currentImg.prev().length;

    img=currentImg.find(".img-scroll-item").attr("src");
    desc=currentImg.find(".img-scroll-item-desc").html();

    var image={
        'img':img,
        'desc':desc,
        'prev':prevCount,
        'next':nextCount
    };

    imageSlider(image);
}

function makeFirstLetterUpper(text){
    return text[0].toUpperCase()+text.substr(1)
}

$("document").ready(function(){

    //view loading progress
    $(document).ajaxStart(function(){
		$("body").css("opacity","0.4");
		$("div#wait").show();
	});

	$(document).ajaxStop(function(){
		$("body").css("opacity","1");
		$("div#wait").hide();
	});

    $("#btn-date-based").on("click", function(){
        $(this).next().fadeToggle();
    });
    
    //activity when ajax starting
    $(document).ajaxStart(function() {
        $("body").css("opacity","0.5");
    });

    //activity when ajax complete
    $(document).ajaxComplete(function(){
        $("body").css("opacity","1");
    });

    //image modal navigation
    var img='';
    var desc='';
    var currentImg='';

    $("#modal-show-attachment").on("click", ".img-scroll-item", function(){
        $(this).closest("#modal-show-attachment").find(".attachment").removeClass("active");

        currentImg=$(this).closest(".attachment").addClass("active");
        console.log("test");
        activeImage(currentImg);
    });

    $(".modal-nav-right").on("click", function(){
        currentImg=$(".attachment.active").removeClass("active").next().addClass("active");
        activeImage(currentImg);
    });

    $(".modal-nav-left").on("click", function(){
        currentImg=$(".attachment.active").removeClass("active").prev().addClass("active");
        activeImage(currentImg);
    });
    //end image modal navigation
    

    //activity when modal button clicked
    $("main").on("click", ".btn-modal", function(){
        if($(this).attr('data-id')!=null){
            var modal=$(this).attr('data-id');
        }else{
            var modal=$(this).attr('id');
        }
        $(this).parent().closest("main").find("#modal-"+modal).css("display","block");  
    });

    //activity when modal button clicked
    $("main").on("click", ".btn-modal-toggle", function(){
        $(this).parent().closest("main").find(".form-modal").hide();
        if($(this).attr('data-id')!=null){
            var modal=$(this).attr('data-id');
        }else{
            var modal=$(this).attr('id');
        }
        $(this).parent().closest("main").find("#modal-toggle-"+modal).toggle();  
    });

    //activity when close button clicked
    $("main").on("click", ".btn-close", function(){
        $(this).parent().closest(".modal").css("display","none");
        //activeModal=false;
    })

    //WIZARD FORM START
    var wizardNum=1;
    var wizardTotal=$(".modal-wizard").length;
    $(".wizard-step").html(wizardNum+"/"+wizardTotal);

    $("form").on("click", ".btn-next", function(){
        var passValidation=true;
        $(this).parent().find(".modal-wizard:nth-of-type("+wizardNum+")").find("input,select").each(function(){
            if($(this).val()==''){
                passValidation=false;
            }
        });
        if(passValidation==true){
            wizardNum+=1;
            $(this).parent().find(".show").removeClass("show").next(".modal-wizard").addClass("show");
            $(".wizard-step").html(wizardNum+"/"+wizardTotal);  
            if(wizardNum>=wizardTotal){
                $(".btn-next").removeClass("btn-next").attr("type","submit").html("Kirim <span class='glyphicon glyphicon-send'>");
            }
            $(".btn-back").css("display","inline");
        }else{
            alert("Semua kolom harus diisi");
        }
    });

    $("form").on("click", ".btn-back", function(){
        wizardNum-=1;
        $(this).parent().find(".show").removeClass("show").prev(".modal-wizard").addClass("show");
        $(".wizard-step").html(wizardNum+"/"+wizardTotal); 
        if(wizardNum<=1){
            $(".btn-back").css("display","none");
        }
        $("button[name~='submit']").attr("type", "button");
        $("button[name~='submit']").html("Lanjut <span class='glyphicon glyphicon-chevron-right'>").addClass("btn-next");
        
    });

    $("form").on("click", ".btn-add-input-form", function(){
        var clone=$(this).closest(".modal-wizard").find(".inline-input:first").clone();
        $(this).closest(".modal-wizard").find(".inline-input:last").after(clone);
        $(this).closest(".modal-wizard").find(".inline-input:last").find("input[type='text'], input[type~='date'], input[type~='number']").val('');
        $(this).closest(".modal-wizard").find(".inline-input:last").append("<button type='button' class='btn btn-danger btn-float'><span class='glyphicon glyphicon-trash'></span></button>");
    });

    $("form").on("click", ".btn-float", function(){
        $(this).closest(".inline-input").remove();
    })

    //WIZARD FORM END

    $("main").on("click", ".btn-modal-ajax", function(){
        
        var modal=$(this).attr('id');
        //documentType determine whether it is tanda terima or the other form
        var documentType=$(".main-data").attr("data-document");
        //documentNumber is the number related to this document type
        var documentNumber=$(".main-data").attr("data-number");
        
        var url;
        var parameter;
        var display='';
        var context;

        switch(modal){
            case 'show-notes':
                url='/form/notes';
                context="notes";
                parameter={document_number:documentNumber,document_type:documentType}

                break;
            case 'create-attachment':
                url='/attachment';
                context="attachment";
                parameter={document_number:documentNumber,document_type:documentType}

                break;
            default:
                    alert("something wrong");
                break;
        }

        $.get(url, parameter, function(data, status){
            var responds=JSON.parse(data);
            console.log(responds)
            if(responds.length<=0){
                display="<p class='text-center'>Belum terdapat data</p>";
            }else if(responds.access==false){
                display="<p class='text-center'>Maaf, Anda tidak memiliki hak akses</p>";
            }else{
                for(var i=0; i<responds.length; i++){
                    if(context=='notes'){
                        display+="<div class='note'><p><strong>"+responds[i].created_by+"</strong><span class='pull-right'>"+responds[i].created_at+"</span></p><blockquote class='clearfix'>"+responds[i].notes+"</blockquote></div>";
                    }else if(context=='attachment'){
                        display+="<div class='note attachment active'><p><strong>"+responds[i].title+"</strong><br><span>"+responds[i].created_at+"</span></p><img src=/public/upload/"+responds[i].upload_file+" class='img-responsive img-scroll-item clearfix'><p class='img-scroll-item-desc text-center'>"+responds[i].description+"</p></div>";
                    }
                }
            }         
            $("#modal-"+modal).find(".modal-list").html(display);
        }); 
    
        $("main").find("#modal-"+modal).css("display","block");

    });

    /* SHOW ATTACHMENT */
    $("select[name~='attachment']").on("change", function(){
        var attachment = $(this).val();
        var responds = '';
        $.get("/dropdown-attachment", {upload_file: attachment}, function(data, status){
            //console.log(data);
            responds = JSON.parse(data);
            var image = "/public/upload/"+responds[0].upload_file;
            var description =responds[0].description;
            //console.log(responds);
            $("#modal-create-attachment").find(".image-appear").empty();
            $("#modal-create-attachment").find(".image-appear").append("<img src="+image+" alt='Attachment' class='img-responsive'><p class='text-center'>"+description+"</p>");
        });
    });
    
    /* 
    $(".btn-ajax").on("click", function(){
        var documentType = $(this).attr('id');
        var context = $(this).parent().closest(".filter").attr("data-filter-by");
        var contextVal = $(this).parent().closest(".filter").attr("data-filter-by-val");

        var url;
        var parameter;
        var display='';

        switch(documentType){
            case 'show-notes':
                url='/form/notes';
                context="notes";
                parameter={document_number:documentNumber,document_type:documentType}

                break;
            case 'show-attachment':
                url='/attachment';
                context="attachment";
                parameter={document_number:documentNumber,document_type:documentType}

                break;
            default:
                    alert("something wrong");
                break;
        }

        $.get(url, parameter, function(data, status){
            var responds=JSON.parse(data);
            console.log(responds)
            if(responds.length<=0){
                display="<p class='text-center'>Belum terdapat data</p>";
            }else if(responds.access==false){
                display="<p class='text-center'>Maaf, Anda tidak memiliki hak akses</p>";
            }else{
                for(var i=0; i<responds.length; i++){
                    if(context=='notes'){
                        display+="<div class='note'><p><strong>"+responds[i].created_by+"</strong><span class='pull-right'>"+responds[i].created_at+"</span></p><blockquote class='clearfix'>"+responds[i].notes+"</blockquote></div>";
                    }else if(context=='attachment'){
                        display+="<div class='note attachment active'><p><strong>"+responds[i].title+"</strong><br><span>"+responds[i].created_at+"</span></p><img src=/public/upload/"+responds[i].upload_file+" class='img-responsive img-scroll-item clearfix'><p class='img-scroll-item-desc text-center'>"+responds[i].description+"</p></div>";
                    }
                }
            }         
            $("#modal-"+modal).find(".modal-list").html(display);
        }); 
    }); */

    $(".column-with-caret").on("click", function(){
        var caret = $(this).find(".caret");
        caret.toggleClass("down")
    });

});
