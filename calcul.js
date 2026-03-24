const display = document.getElementById("display");
const historyEl = document.getElementById("history");

let lastResult = "";

function setDisplay(value) {
  display.value = value;
}

function appendToDisplay(value) {
  if (display.value === "0" || display.value === "Error") {
    display.value = "";
  }

  if (display.value.length > 24) return; // prevent overflow

  display.value += value;
}

function clearDisplay() {
  setDisplay("0");
  lastResult = "";
  historyEl.textContent = "";
}

function deleteLast() {
  if (display.value.length <= 1) {
    setDisplay("0");
    return;
  }

  display.value = display.value.slice(0, -1);
}

function safeEvaluate(expression) {
  const cleaned = expression.replace(/÷/g, "/").replace(/×/g, "*").replace(/[^0-9.+\-*/()% ]/g, "");
  if (!cleaned.trim()) throw new Error("Empty expression");

  // Prevent repeated operators, leading operator except '-' and parentheses
  if (/--|\+\+|\*\*|\/\//.test(cleaned)) throw new Error("Invalid operators");

  // eslint-disable-next-line no-new-func
  return Function(`"use strict"; return (${cleaned})`)();
}

function calculate() {
  try {
    const expression = display.value.trim();
    const result = safeEvaluate(expression);

    if (Number.isFinite(result)) {
      lastResult = result;
      setDisplay(String(result));
      historyEl.textContent = `${expression} = ${result}`;
      display.classList.add("pop");
      setTimeout(() => display.classList.remove("pop"), 220);
    } else {
      throw new Error("Math error");
    }
  } catch (error) {
    setDisplay("Error");
    historyEl.textContent = "Invalid expression";
  }
}

function handleButton(event) {
  const button = event.target.closest("button");
  if (!button) return;

  const action = button.dataset.action;
  const value = button.dataset.value;

  if (action === "clear") {
    clearDisplay();
  } else if (action === "delete") {
    deleteLast();
  } else if (action === "equals") {
    calculate();
  } else if (value) {
    appendToDisplay(value);
  }
}

function handleKeyboard(event) {
  const key = event.key;

  if (/^[0-9]$/.test(key)) {
    appendToDisplay(key);
  } else if (/[+\-*/().%]/.test(key)) {
    appendToDisplay(key);
  } else if (key === "Enter" || key === "=") {
    event.preventDefault();
    calculate();
  } else if (key === "Backspace") {
    deleteLast();
  } else if (key === "Escape") {
    clearDisplay();
  }
}

function init() {
  clearDisplay();
  document.getElementById("keypad").addEventListener("click", handleButton);
  window.addEventListener("keydown", handleKeyboard);
}

init();