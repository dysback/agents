/**
 * @file login.js
 * Handles login form submission, calls the auth API, stores the JWT,
 * and redirects to the home page on success.
 */

const API_URL = '/api/login';
const TOKEN_KEY = 'jwt_token';

/**
 * Displays an error message in the form's error container.
 *
 * @param {string} message - The error text to display.
 * @returns {void}
 */
function showError(message) {
  const el = document.getElementById('login-error');
  el.textContent = message;
  el.removeAttribute('hidden');
}

/**
 * Hides the error container.
 *
 * @returns {void}
 */
function hideError() {
  const el = document.getElementById('login-error');
  el.setAttribute('hidden', '');
}

/**
 * Submits login credentials to the API and returns the JWT token.
 *
 * @param {string} username - The username entered by the user.
 * @param {string} password - The password entered by the user.
 * @returns {Promise<{token: string}>} Resolves with the token object on success.
 * @throws {Error} If the response is not OK or a network error occurs.
 */
async function submitLogin(username, password) {
  const response = await fetch(API_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password }),
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || 'Login failed');
  }

  return data;
}

/**
 * Handles the login form submit event.
 *
 * @param {SubmitEvent} event - The form submit event.
 * @returns {Promise<void>}
 */
async function handleSubmit(event) {
  event.preventDefault();
  hideError();

  const username = /** @type {HTMLInputElement} */ (document.getElementById('username')).value.trim();
  const password = /** @type {HTMLInputElement} */ (document.getElementById('password')).value;

  if (!username || !password) {
    showError('Please enter username and password.');
    return;
  }

  const submitBtn = /** @type {HTMLButtonElement} */ (event.target.querySelector('button[type="submit"]'));
  submitBtn.disabled = true;

  try {
    const { token } = await submitLogin(username, password);
    localStorage.setItem(TOKEN_KEY, token);
    window.location.href = '/';
  } catch (err) {
    showError(/** @type {Error} */ (err).message);
  } finally {
    submitBtn.disabled = false;
  }
}

/**
 * Bootstraps the login page by attaching the form submit handler.
 *
 * @returns {void}
 */
function init() {
  const form = document.getElementById('login-form');
  if (!form) return;
  form.addEventListener('submit', handleSubmit);
}

init();
