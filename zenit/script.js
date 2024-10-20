function openModal() {
  document.getElementById("reservationModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("reservationModal").style.display = "none";
}

window.onclick = function(event) {
  if (event.target == document.getElementById("reservationModal")) {
      document.getElementById("reservationModal").style.display = "none";
  }
}


let currentStartIndex = 0;
const locations = document.querySelectorAll('.location');
const locationsToShow = 4; // Počet lokalít, ktoré sa majú zobraziť naraz

function showNextLocations() {
    // Skryjeme aktuálne viditeľné lokácie
    locations.forEach(location => location.classList.remove('visible'));
    
    // Zobrazíme nasledujúce 4 lokácie
    for (let i = 0; i < locationsToShow; i++) {
        let index = (currentStartIndex + i) % locations.length; // Cyklus cez lokácie
        locations[index].classList.add('visible');
    }
    
    // Posunieme začiatok okna pre ďalší cyklus
    currentStartIndex = (currentStartIndex + 1) % locations.length;
}

// Nastavenie počiatočných viditeľných lokácií
showNextLocations();

// Interval na prepínanie lokácií každých 5 sekúnd
setInterval(showNextLocations, 5000);


function openModal() {
  document.getElementById("reservationModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("reservationModal").style.display = "none";
}

window.onclick = function(event) {
  if (event.target == document.getElementById("reservationModal")) {
      document.getElementById("reservationModal").style.display = "none";
  }
}

// Prepočítať cenu na základe typu skipasu a počtu osôb
const skiPassType = document.getElementById("ski_pass_type");
const numberOfDays = document.getElementById("number_of_days");
const numberOfPeople = document.getElementById("number_of_people");
const priceAmount = document.getElementById("price_amount");

// Predpokladajme základné ceny
const basePrices = {
  'child': 70, // cena dieťaťa
  'junior': 80, // cena juniora
  'adult': 100, // základná cena dospelého
  'senior': 85  // cena seniora
};

function updatePrice() {
  let totalPrice = 0;
  const selectedPassType = skiPassType.value;
  const peopleCount = parseInt(numberOfPeople.value, 10); // Prevod na celé číslo

  // Logika pre výpočet ceny
  switch (selectedPassType) {
      case '1_day':
          numberOfDays.value = 1; // Nastaviť počet dní na 1
          totalPrice = basePrices['adult']; // Nastaviť cenu na dospelého
          break;
      case 'multi_day':
          if (numberOfDays.value < 1) {
              numberOfDays.value = 1;
          }
          totalPrice = basePrices['adult'] * numberOfDays.value * 0.9; // 10% zľava
          break;
      case 'season':
          totalPrice = basePrices['adult'] * 10; // 10 násobok ceny
          break;
  }

  // Pridávanie ceny podľa počtu osôb
  totalPrice *= peopleCount; // Vynásobíme celkovú cenu počtom osôb

  priceAmount.innerText = totalPrice.toFixed(2); // Zobrazí cenu s 2 desatinnými miestami
}

// Event Listeners
skiPassType.addEventListener('change', updatePrice);
numberOfPeople.addEventListener('input', updatePrice);
numberOfDays.addEventListener('input', updatePrice);

// Inicializácia ceny
updatePrice();
