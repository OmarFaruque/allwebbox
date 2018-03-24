<?php 

$text = <<<EOT
<div id="place_questions_n" style="display: none;" class="place_questions">
<div class="sqpart">
    <label>Question : n</label>
    <input name="name_question[n]" class="form-control" placeholder="Enter Your Question ?" type="text"> 
</div>
<div class="sqpart">
    <label>Required : 
    <input type="checkbox" name="req_question[n]" value="1" /></label> 
</div>
<div class="sqpart last">
    <label>Answer Type : </label>          
    <select name="type_question[n]" class="form-control type_question"  id="1" onChange="showAnsType(this.value,n)" data-position="0">
                    <option  selected="selected">Select Your Answer Format </option>
                    <option value="1">Text</option>
    				<option value="2">Description</option>
    				<option value="3">Single selection</option>
    				<option value="4">Multiple selection</option>
    				<option value="5">Number</option>
    				<option value="6">Date</option>
    				<option value="7">Email</option>
    </select>
</div>
<label></label>
<select name="num_options_question[n]" id="questypes_3" style="display: none;" class="questypes form-control" data-position="0" onChange="showOptionAnswers(this.value,n)" >
 <option value="-1" >How many Options:</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
 </select>
 <select name="num_options_question_m[n]" id="questypes_4" style="display: none;" class="questypes form-control" onChange="showOptionAnswers(this.value,n)" data-position="0">
 <option value="-1">Selecciona el número de opciones</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
 </select>
 <br/>
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_1" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_2" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_3" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_4" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_5" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_6" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_7" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_8" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_9" class="form-control"/> 
<input type="" class="options_answer form-control" name="option_answ_n[]" id="options_answer_10" class="form-control"/> 
</div>
EOT;
 
for($i=1;$i<15;$i++) {	
	echo  str_replace("showOptionAnswers(this.value,n)","showOptionAnswers(this.value,".$i.")",
	      str_replace("showOptionAnswers(this.value,n)","showOptionAnswers(this.value,".$i.")",
		  str_replace("showAnsType(this.value,n)","showAnsType(this.value,".$i.")",
                  str_replace("Question : n","Question : ".$i."",
		  str_replace("type_question[n]","type_question[".$i."])",
          str_replace("req_question[n]","req_question[".$i."])",
		  str_replace("num_options_question[n]","num_options_question[".$i."])",
		  str_replace("num_options_question_m[n]","num_options_question_m[".$i."])",
		  str_replace("option_answ_n[]","option_answ_".$i."[]",
          str_replace("name_question[n]","name_question[".$i."])",
		  str_replace("place_questions_n","place_questions_".$i,$text)))))))))));
}
 
?>

