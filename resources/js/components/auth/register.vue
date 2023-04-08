<template>
    <div class="registration-container">
      <h1>Register</h1>
      <form>
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>
      </form>
    </div>
</template>

<style>
body {
  background-color: #f1f1f1;
}

.registration-container {
  width: 400px;
  margin: auto;
  margin-top: 100px;
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}

h1 {
  text-align: center;
  color: #555;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  color: #555;
}

input {
  padding: 10px;
  border-radius: 3px;
  border: 1px solid #ccc;
  margin-bottom: 20px;
}

button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 3px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background-color: #3e8e41;
}
</style>