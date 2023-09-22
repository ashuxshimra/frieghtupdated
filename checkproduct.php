<?php 
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fibble - Supply Chain DAPP</title>
    <link rel="SHORTCUT ICON" href="images/fibble.png" type="image/x-icon" />
    <link rel="ICON" href="images/fibble.png" type="image/ico" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/mdbmin.css" rel="stylesheet">
    <link href="css/mdb.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <style>
      /* Add custom CSS for the Pay button */
      .payBtn {
        position: fixed;
        bottom: 20px; /* Adjust this value as needed for the vertical position */
        left: 50%; /* Center horizontally */
        transform: translateX(-50%);
        background: transparent;
        border: 2px solid dark;
        font-size: 18px;
        color: #fff; /* Text color */
        cursor: pointer;
        z-index: 9999; /* Ensure the button appears above other elements */
      }
      .payBtn:hover {
        background-color: #007bff; /* New background color on hover */
        color: red; /* New text color on hover */
      }
    </style>
  </head>
  <?php
  if(isset($_SESSION['role'])){
  ?>
  <body class="violetgradient">
    <?php
    include "navbar.php"
    ?>
    <center>
      <div class="customalert">
        <div class="alertcontent">
          <div id="alertText"> &nbsp; </div>
          <img id="qrious">
          <div id="bottomText" style="margin-top: 10px; margin-bottom: 15px;"> &nbsp; </div>
          <button id="closebutton" class="formbtn"> OK </button>
        </div>
      </div>
    </center>
    <div>
      <center>
        <div class="centered">
          <form role="form" autocomplete="off">
            <input type="text" id="searchText" class="searchBox" placeholder="Enter Product Code" onkeypress="isInputNumber(event)" required>
            <label class="qrcode-text-btn" style="width:4%;display:none;">
              <input type="file" accept="image/*" id="selectedFile" style="display:none" capture="environment" onchange="openQRCamera(this);" tabindex="-1">
            </label>
            <button type="submit" id="searchButton" class="searchBtn"><i class="fa fa-search"></i></button>
          </form>
          <button class="qrbutton" onclick="document.getElementById('selectedFile').click();">
            <i class='fa fa-qrcode'></i> Scan QR
          </button>
          <br><br>
          <p id="database" class="cardstyle">
            No Data to Display
          </p>
        </div>
      </center>
    </div>
    <div class='box'>
      <div class='wave -one'></div>
      <div class='wave -two'></div>
      <div class='wave -three'></div>
    </div>
    <!-- Pay button -->
    <button id="payButton" class="payBtn">One Time Consumer Pay🪙</button>
    <?php } else {
      include 'redirection.php';
      redirect("index.php");
    } ?>
    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/mdb.min.js"></script>
    <script src="web3.min.js"></script>
    <script src="app.js"></script>
    <!-- QR Code Reader -->
    <script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>
    <!-- Web3 Injection -->
    <script>
      web3 = new Web3(new Web3.providers.HttpProvider('HTTP://127.0.0.1:7545'));
      // Set the Contract
      var contract = new web3.eth.Contract(contractAbi, contractAddress);
      $(".cardstyle").hide();
      // Change the Data
      $('form').on('submit', function(event) {
        event.preventDefault(); // to prevent page reload when form is submitted
        greeting = $('input').val();
        console.log(greeting);
        contract.methods.searchProduct(greeting).call(function(err, result) {
          console.log(err, result);
          $(".cardstyle").show("fast", "linear");
          $("#database").html(result);
        });
      });
      function isInputNumber(evt){
        var ch = String.fromCharCode(evt.which);
        if(!(/[0-9]/.test(ch))){
          evt.preventDefault();
        }
      }
      $("#closebutton").on("click", function(){
        $(".customalert").hide("fast", "linear");
      });
      // Existing JavaScript code...
      // Add a click event listener to the Pay button
      $("#payButton").on("click", function() {
        // Check if Web3 is available
        if (typeof web3 !== 'undefined') {
          // Use the injected Web3 provider
          web3 = new Web3(web3.currentProvider);
          var contractAddress = '0xfE2ee89AB644EF7125815f0347B807E2DA91886D';
          var paymentContractAbi = [
            {
              "inputs": [],
              "name": "pay",
              "outputs": [],
              "stateMutability": "payable",
              "type": "function"
            },
            {
              "inputs": [],
              "stateMutability": "nonpayable",
              "type": "constructor"
            },
            {
              "anonymous": false,
              "inputs": [
                {
                  "indexed": true,
                  "internalType": "address",
                  "name": "from",
                  "type": "address"
                },
                {
                  "indexed": false,
                  "internalType": "uint256",
                  "name": "amount",
                  "type": "uint256"
                }
              ],
              "name": "PaymentReceived",
              "type": "event"
            },
            {
              "inputs": [
                {
                  "internalType": "address",
                  "name": "",
                  "type": "address"
                }
              ],
              "name": "balances",
              "outputs": [
                {
                  "internalType": "uint256",
                  "name": "",
                  "type": "uint256"
                }
              ],
              "stateMutability": "view",
              "type": "function"
            },
            {
              "inputs": [],
              "name": "owner",
              "outputs": [
                {
                  "internalType": "address",
                  "name": "",
                  "type": "address"
                }
              ],
              "stateMutability": "view",
              "type": "function"
            }
          ];
          // Check if Metamask is installed and unlocked
          web3.eth.getAccounts(function(error, accounts) {
            if (!error && accounts.length > 0) {
              // Assuming you have deployed your PaymentContract
              var contract = new web3.eth.Contract(paymentContractAbi, contractAddress);
              // Call the 'pay' function on the smart contract
              contract.methods.pay().send({ from: accounts[0], value: web3.utils.toWei('1', 'ether') }, function(error, transactionHash) {
                if (!error) {
                  // Payment successful
                  showAlert("Payment successful. Transaction Hash: " + transactionHash);
                  showSuccessToast("Payment successful!,Consumer Item Received ");
                } else {
                  // Payment failed
                  showAlert("Payment failed: " + error.message);
                  showErrorToast("Consumer already made Payment !");
                }
              });
            } else {
              // Metamask is not available or unlocked
              showAlert("Please install and unlock Metamask to make the payment.");
              showErrorToast("Metamask not available or unlocked.");
            }
          });
        } else {
          // Web3 is not available
          showAlert("Web3 is not available. Please install Metamask.");
        }
      });
      function showSuccessToast(message) {
        Toastify({
          text: message,
          duration: 5000,
          close: true,
          gravity: "top", // You can change the toast position as needed
          backgroundColor: "green",
        }).showToast();
      }
      // Function to show an error toast notification
      function showErrorToast(message) {
        Toastify({
          text: message,
          duration: 5000,
          close: true,
          gravity: "top", // You can change the toast position as needed
          backgroundColor: "red",
        }).showToast();
      }
      // Existing JavaScript code...
      function openQRCamera(node) {
        var reader = new FileReader();
        reader.onload = function() {
          node.value = "";
          qrcode.callback = function(res) {
            if (res instanceof Error) {
              alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
            } else {
              node.parentNode.previousElementSibling.value = res;
              document.getElementById('searchButton').click();
            }
          };
          qrcode.decode(reader.result);
        };
        reader.readAsDataURL(node.files[0]);
      }
      function showAlert(message) {
        $("#alertText").html(message);
        $("#qrious").hide();
        $("#bottomText").hide();
        $(".customalert").show("fast", "linear");
      }
      $("#aboutbtn").on("click", function() {
        showAlert("A Decentralised End to End Logistics Application that stores the whereabouts of product at every freight hub to the Blockchain. At consumer end, customers can easily scan product's QR CODE and get complete information about the provenance of that product hence empowering	consumers to only purchase authentic and quality products Made with ♥ by : Bandla sai dinesh, Potta venkata nivas, T Prapul, Krishna prasad");
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  </body>
</html>
