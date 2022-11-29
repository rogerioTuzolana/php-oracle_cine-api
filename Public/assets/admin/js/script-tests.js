
const cell = document.querySelector(".cell");
const answer = document.querySelector(".answer");
const span_data = document.querySelector("#span-data");
const many_option = document.querySelector("#many-option");

$("#type").on('change', function () {
  let cellSelected = $("#cell-number").val();
  let type_question = $("#type");
  if (parseInt(type_question.val())==1) {
    cell.style.display="none";
    answer.style.display="block";
    span_data.style.display="none";
    if (many_option) {
      many_option.style.display="none";
    }
  }else{
    cell.style.display="block";
    answer.style.display="none";
    span_data.style.display="block";
    if (many_option) {
      many_option.style.display="block";     
    }
  }
})

$(".sub-option").on("click",function() {
  const option_id = $(this).attr("data-option");
  $.ajax({
    method: "POST",
    url: '',
    data:{
      ajax_sub_option:true,
      option_id: option_id,
    }
  }).
  done(function(response){

    window.location.reload();

  }).
  fail(function (response){
    console.log("Error: ");
    console.log(response);
  })
})

function sub_option(option) {
  $("."+option).remove();
}

$("#many-option").on("click",function() {
  
  var span_model = $("#span-model2").html();
  var span_model_str = span_model.toString();
  var cells = $("#cell-number").val();
  var option_total = $("#option-total").val();
  var new_option_total = $("#newOption-total").val();

  var i = 1;
  var text = '';

  if (option_total!="") {
    option_total = parseInt(option_total);
    text = text + span_model_str.replace(/--1/g, '-' + (option_total+i)+'').replace(/1ª/g, + (option_total+i)+'' + 'ª').replace(/option-answer-clss/g, 'option-answer-clss'+(option_total+i));
    $("#option-total").val(option_total+i)
    $("#newOption-total").val(i);
  
  }
  $("#span-real").append(text);
});

function inputCell() {
  
  var span_model = $("#span-model").html();
  var span_model_str = span_model.toString();
  var cells = $("#cell-number").val();
  var option_total = parseFloat($("#option-total").val());
  var i = 1;
  if(option_total){
    i = option_total + 1;
  }
  var text = '';
  
  while (i <= cells) {
    text = text + span_model_str.replace(/-0/g, '-' + i.toString()).replace(/1ª/g, + i.toString() + 'ª');
    i++;
  }
  
  //console.log($("#span-model").html());
  $("#span-real").html(text);
}


$("#course_id").on("change",function (e) {

    const course_id = $(this).val();
    $.ajax({
        method: "GET",
        url: '',
        data:{
          ajax_admin_vid:true,
          course_id: course_id,
        }
    }).
      done(function(response){
        let options = '<option value="0"></option>';
        //console.log(response);
        $("#type_test0").remove();
        $("#type_test").remove();
        if (response != "0") {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const element = response[key];
              //console.log(element.id);
              options += '<option value="'+element.id+'">'+element.title+'</option>';
            }
          }
  
          $("#span-type-model").append(
            '<select name="type_test" id="type_test" class="form-control">'+
              options+
            '</select>'
          );       
        }else{
          $("#span-type-model").append(
            '<select name="type_test" id="type_test" class="form-control">'+
              options+
            '</select>'
          );
        }

      }).
      fail(function (response){
        console.log("Error: ");
        console.log(response);
      })

})

