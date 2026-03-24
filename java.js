const form = document.getElementById('regform');
const messageEl = document.getElementById('message');

const rules = {
  name: {
    validate: value => value.trim().length >= 3,
    error: 'Please enter at least 3 characters for name.'
  },
  email: {
    validate: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    error: 'Please enter a valid email address.'
  },
  gender: {
    validate: () => {
      return Array.from(document.getElementsByName('gender')).some(i => i.checked);
    },
    error: 'Please select your gender.'
  },
  password: {
    validate: value => value.trim().length >= 6,
    error: 'Password must be at least 6 characters.'
  },
  phone: {
    validate: value => /^\+?[0-9\s\-]{7,15}$/.test(value.trim()),
    error: 'Enter a valid phone number.'
  },
  address: {
    validate: value => value.trim().length >= 5,
    error: 'Enter your address.'
  },
  terms: {
    validate: () => document.getElementById('terms').checked,
    error: 'You must agree to the terms.'
  },
  department: {
    validate: value => value.trim().length > 0,
    error: 'Please pick a department.'
  }
};

function resetErrors() {
  Object.keys(rules).forEach(key => {
    const el = document.getElementById(`error-${key}`);
    if (el) el.textContent = '';
  });
  messageEl.textContent = '';
}

function validateForm() {
  let isValid = true;

  const nameInput = document.getElementById('name');
  const emailInput = document.getElementById('email');
  const deptInput = document.getElementById('dept');

  resetErrors();

  if (!rules.name.validate(nameInput.value)) {
    document.getElementById('error-name').textContent = rules.name.error;
    isValid = false;
  }

  if (!rules.email.validate(emailInput.value)) {
    document.getElementById('error-email').textContent = rules.email.error;
    isValid = false;
  }

  if (!rules.gender.validate()) {
    document.getElementById('error-gender').textContent = rules.gender.error;
    isValid = false;
  }

  const passwordInput = document.getElementById('password');
  const phoneInput = document.getElementById('phone');
  const addressInput = document.getElementById('address');
  const termsInput = document.getElementById('terms');

  if (!rules.password.validate(passwordInput.value)) {
    document.getElementById('error-password').textContent = rules.password.error;
    isValid = false;
  }

  if (!rules.phone.validate(phoneInput.value)) {
    document.getElementById('error-phone').textContent = rules.phone.error;
    isValid = false;
  }

  if (!rules.address.validate(addressInput.value)) {
    document.getElementById('error-address').textContent = rules.address.error;
    isValid = false;
  }

  if (!rules.terms.validate()) {
    document.getElementById('error-terms').textContent = rules.terms.error;
    isValid = false;
  }

  if (!rules.department.validate(deptInput.value)) {
    document.getElementById('error-dept').textContent = rules.department.error;
    isValid = false;
  }

  return isValid;
}

form.addEventListener('submit', function (event) {
  event.preventDefault();

  if (!validateForm()) {
    messageEl.textContent = 'Check the fields highlighted above.';
    messageEl.style.color = '#fca5a5';
    return;
  }

  messageEl.textContent = 'Registration successful! Thank you.';
  messageEl.style.color = '#86efac';

  const formData = {
    name: nameInput.value.trim(),
    email: emailInput.value.trim(),
    gender: Array.from(document.getElementsByName('gender')).find(i => i.checked)?.value || '',
    department: deptInput.value,
    phone: document.getElementById('phone').value.trim(),
    address: document.getElementById('address').value.trim(),
  };

  console.log('Submitted student form:', formData);

  const summary = document.createElement('div');
  summary.style.marginTop = '0.7rem';
  summary.style.padding = '0.75rem';
  summary.style.border = '1px solid rgba(56,189,248,0.5)';
  summary.style.borderRadius = '10px';
  summary.style.background = 'rgba(15, 23, 42, 0.4)';
  summary.innerHTML = `
    <strong>Saved data:</strong>
    <p>${formData.name} — ${formData.email}</p>
    <p>${formData.gender} | ${formData.department}</p>
    <p>${formData.phone} | ${formData.address}</p>
  `;

  messageEl.parentElement.appendChild(summary);

  form.reset();
});

const printBtn = document.getElementById('printBtn');
printBtn.addEventListener('click', () => window.print());
