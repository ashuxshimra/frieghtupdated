pragma solidity ^0.8.0;

contract SupplyChain {
    
    event Added(uint256 index);
    
    struct State{
        string description;
        address person;
    }
    
    struct Product{
        address creator;
        string productName;
        uint256 productId;
        string date;
        uint256 totalStates;
        mapping (uint256 => State) positions;
    }
    
 address public owner;
    mapping(address => uint256) public balances;
    
    event PaymentReceived(address indexed from, uint256 amount);
    
    constructor() {
        owner = msg.sender;
    }
    
    function pay() public payable {
        require(balances[msg.sender] == 0, "Payment already made.");
        require(msg.value > 0, "Payment amount must be greater than zero.");
        
        balances[msg.sender] = msg.value;
        emit PaymentReceived(msg.sender, msg.value);
    }



    mapping(uint => Product) allProducts;
    uint256 items=0;
    
    function concat(string memory _a, string memory _b) public returns (string memory){
        bytes memory bytes_a = bytes(_a);
        bytes memory bytes_b = bytes(_b);
        string memory length_ab = new string(bytes_a.length + bytes_b.length);
        bytes memory bytes_c = bytes(length_ab);
        uint k = 0;
        for (uint i = 0; i < bytes_a.length; i++) bytes_c[k++] = bytes_a[i];
        for (uint i = 0; i < bytes_b.length; i++) bytes_c[k++] = bytes_b[i];
        return string(bytes_c);
    }   
    
function newItem(string memory _text, string memory _date) public returns (bool) {
    Product storage newProduct = allProducts[items];
    newProduct.creator = msg.sender;
    newProduct.totalStates = 0;
    newProduct.productName = _text;
    newProduct.productId = items;
    newProduct.date = _date;

    // Initialize the mapping here (optional, but good practice)
    // newProduct.positions = new mapping(uint256 => State);

    items = items + 1;
    emit Added(items - 1);
    return true;
}


    
    function addState(uint _productId, string memory info) public returns (string memory) {
        require(_productId<=items);
        
        State memory newState = State({person: msg.sender, description: info});
        
        allProducts[_productId].positions[ allProducts[_productId].totalStates ]=newState;
        
        allProducts[_productId].totalStates = allProducts[_productId].totalStates +1;
        return info;
    }
    
    function searchProduct(uint _productId) public returns (string memory) {

        require(_productId<=items);
        string memory output="Product Name: ";
        output=concat(output, allProducts[_productId].productName);
        output=concat(output, "<br>Manufacture Date: ");
        output=concat(output, allProducts[_productId].date);
        
        for (uint256 j=0; j<allProducts[_productId].totalStates; j++){
            output=concat(output, allProducts[_productId].positions[j].description);
        }
        return output;
        
    }
    
}