<template>
    <div class="notfound-container">
        <h1 class="notfound-heading">404</h1>
        <p class="notfound-message">Page not found.</p>
        <p><router-link to="/" class="notfound-link">Go back to the home page</router-link></p>
    </div>
</template>
<style>
/** not found page **/
.notfound-body {
  background-color: #f1f1f1;
  font-family: Arial, sans-serif;
  font-size: 16px;
  line-height: 1.5;
  color: #333;
  margin: 0;
  padding: 0;
}

.notfound-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 50px;
  text-align: center;
}

.notfound-heading {
  font-size: 4em;
  margin-bottom: 0;
}

.notfound-message {
  font-size: 1.5em;
  margin-top: 0;
}

.notfound-link {
  color: #0066cc;
  text-decoration: none;
}

.notfound-link:hover {
  text-decoration: underline;
}
/** not found end **/
</style>