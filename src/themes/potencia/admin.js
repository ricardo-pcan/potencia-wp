/*
jQuery( document ).ready(function($) {
  var grade_elementary = [
    {display: '1°', value:'pri_1'},
    {display: '2°', value:'pri_2'},
    {display: '3°', value:'pri_3'}
  ];

  var grade_highschool = [
    {display: '1°', value:'sec_1'},
    {display: '2°', value:'sec_2'},
    {display: '3°', value:'sec_3'},
    {display: '4°', value:'sec_4'},
    {display: '5°', value:'sec_5'},
    {display: '6°', value:'sec_6'}
  ];

  $('#level_select').change(function () {
    var level = $(this).val();
    switch(level) {
      case 'elementary':
        list(grade_elementary);
        break;
      case 'highschool':
        list(grade_highschool);
        break;
    }
  });

  function list(array_list) {
    $(array_list).each(function (i) {
      $('#grade_select_field').append("<input type='radio' name='grade_select' value="+array_list[i].value+">"+array_list[i].display+"</option><br>");
    });
  }
});
