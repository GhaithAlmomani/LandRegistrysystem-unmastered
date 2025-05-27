// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract PropertyRegistry {
    // Property structure
    struct Property {
        uint256 id;
        address owner;
        string description;
        string latitude; // Latitude in string format for precision
        string longitude; // Longitude in string format for precision
    }

    // State variables
    Property[] private properties; // List of properties
    mapping(address => bool) public authorizedEmployees; // Employee authorization mapping
    address public admin; // Admin address

    // Events
    event PropertyRegistered(
        uint256 id,
        address owner,
        string description,
        string latitude,
        string longitude,
        address registeredBy
    );
    event OwnershipTransferred(
        uint256 id,
        address previousOwner,
        address newOwner,
        address authorizedEmployee
    );
    event EmployeeAuthorizationUpdated(address employee, bool isAuthorized);

    // Modifiers
    modifier onlyAdmin() {
        require(msg.sender == admin, "Only admin can perform this action");
        _;
    }

    modifier onlyAuthorizedEmployee() {
        require(
            authorizedEmployees[msg.sender],
            "Only authorized employees can perform this action"
        );
        _;
    }

    // Constructor to set admin
    constructor() {
        admin = msg.sender; // Deploying address is the admin
    }

    // Function to authorize or revoke an employee (admin only)
    function setEmployeeAuthorization(
        address _employee,
        bool _isAuthorized
    ) public onlyAdmin {
        authorizedEmployees[_employee] = _isAuthorized;
        emit EmployeeAuthorizationUpdated(_employee, _isAuthorized);
    }

    // Function to register a property (authorized employees only)
    function registerProperty(
        uint256 _id,
        address _owner,
        string memory _description,
        string memory _latitude,
        string memory _longitude
    ) public onlyAuthorizedEmployee {
        properties.push(Property(_id, _owner, _description, _latitude, _longitude));
        emit PropertyRegistered(
            _id,
            _owner,
            _description,
            _latitude,
            _longitude,
            msg.sender
        );
    }

    // Function to transfer property ownership (authorized employees only)
    function transferOwnership(
        uint256 _id,
        address _previoustOwner,
        address _newOwner
    ) public onlyAuthorizedEmployee {
        require(_id < properties.length, "Property does not exist");

        Property storage property = properties[_id];
        address previousOwner = property.owner;

        // Ensure the provided current owner matches the actual owner
        require(previousOwner == _previoustOwner, "Current owner does not match");
        require(previousOwner != _newOwner, "Cannot transfer to the same owner");

        property.owner = _newOwner;

        emit OwnershipTransferred(_id, previousOwner, _newOwner, msg.sender);
    }

    // Get all properties
    function getAllProperties() public view returns (Property[] memory) {
        return properties;
    }

    // Get a property by ID
    function getPropertyById(
        uint256 _id
    ) public view returns (uint256, address, string memory, string memory, string memory) {
        require(_id < properties.length, "Property does not exist");

        Property memory property = properties[_id];
        return (
            property.id,
            property.owner,
            property.description,
            property.latitude,
            property.longitude
        );
    }

    // Get the total count of properties
    function getPropertyCount() public view returns (uint256) {
        return properties.length;
    }

    // Get full information of a property by ID
    function getPropertyInfo(
        uint256 _id
    )
        public
        view
        returns (
            uint256 id,
            address owner,
            string memory description,
            string memory latitude,
            string memory longitude,
            bool isAuthorizedForTransfer
        )
    {
        require(_id < properties.length, "Property does not exist");

        Property memory property = properties[_id];
        return (
            property.id,
            property.owner,
            property.description,
            property.latitude,
            property.longitude,
            authorizedEmployees[msg.sender]
        );
    }
}
