function quantitySubmit(){
    const quantity = document.querySelector('input').value;
    let x = 1;
    while(x <= quantity) {
        createExercise(x);
        x++;    
    }
    // const endButton = document.createElement("button");
    // endButton.classList.add('btn');
    $('#form').append('<button class="btn btn-primary" id="endButton" type="submit" name="submit">Submit</button>');
}
function createExercise(number){
  let form = document.getElementById("form");
  const newDiv = document.createElement("div");
  newDiv.classList.add('container');

  let exerciseNumber = document.createTextNode(number);
  let br = document.createElement("br");

  let exText = document.createTextNode("Exercise:");
  const exerciseInput = document.createElement("input");
  exerciseInput.classList.add('form-control');

  let amText = document.createTextNode("Amount:");
  const amountInput = document.createElement("input");
  amountInput.classList.add('form-control');

  let timeText = document.createTextNode("Time:");
  const timeInput = document.createElement("input");
  timeInput.classList.add('form-control');

//   newDiv.appendChild(br);
  $(newDiv).html(number+'<br>');
  newDiv.appendChild(exText);
  newDiv.appendChild(exerciseInput);
  $(exerciseInput).attr('name', 'exerNumber_'+number)
  newDiv.appendChild(amText);
  newDiv.appendChild(amountInput);
  $(amountInput).attr('name', 'amNumber_'+number)
  newDiv.appendChild(timeText);
  newDiv.appendChild(timeInput);
  $(timeInput).attr('name', 'timeNumber_'+number)
  $(newDiv).append('<br>');
//   newDiv.appendChild(br);
$('#form').append(newDiv);
  const currentDiv = document.getElementById("footer");
//   form.insertAdjacentHTML("afterend", newDiv);
}


function setExerciseCount() {
    let quantity = document.getElementById("exerciseQuantity").value;
    let exerciseCountField = document.getElementById("exercise_count");
    let exerciseFields = document.getElementById("exerciseFields");

    quantity = parseInt(quantity);
    if (isNaN(quantity) || quantity <= 0 || quantity > 20) {
        alert("Enter a valid number between 1 and 20.");
        return false;
    }

    exerciseCountField.value = quantity;
    exerciseFields.innerHTML = ""; 

    for (let i = 1; i <= quantity; i++) {
        let html = `
            <div class="mb-3">
                <label>Exercise ${i}</label>
                <input type="text" name="exerNumber_${i}" class="form-control" placeholder="Exercise type" required>
                <input type="text" name="amNumber_${i}" class="form-control mt-2" placeholder="Amount (e.g., 10 reps)" required>
                <input type="text" name="timeNumber_${i}" class="form-control mt-2" placeholder="Time (e.g., 10 min)" required>
            </div>
        `;
        exerciseFields.innerHTML += html;
    }
    return false;
}
