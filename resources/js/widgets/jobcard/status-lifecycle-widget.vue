<style scoped>

    * {
    margin: 0;
    padding: 0;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    font-size: 12px;
    }

    body {
    text-align: center;
    background-color: #34495e;
    }

    h1 {
    font-weight: 100;
    font-size: 32px;
    padding: 40px;
    color: #fff;
    }

    #breadcrumb {
    list-style: none;
    display: inline-block;
    }
    #breadcrumb >>> .icon {
    font-size: 14px;
    }
    #breadcrumb >>> li {
    float: left;
    margin-bottom:5px;
    }
    #breadcrumb >>> li a {
    color: #FFF;
    display: block;
    background: #3498db;
    text-decoration: none;
    position: relative;
    height: 40px;
    line-height: 40px;
    padding: 0 10px 0 5px;
    text-align: center;
    margin-right: 23px;
    }
    /*
    #breadcrumb >>> li:nth-child(even) a {
    background-color: #19be6b;
    }
    #breadcrumb >>> li:nth-child(even) a:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li:nth-child(even) a:after {
    border-left-color: #19be6b;
    }
    */
    #breadcrumb >>> li.active a {
    background-color: #19be6b;
    }
    #breadcrumb >>> li.active a:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li.active a:after {
    border-left-color: #19be6b;
    }
    #breadcrumb >>> li:first-child a {
    padding-left: 15px;
    -moz-border-radius: 4px 0 0 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px 0 0 4px;
    }
    #breadcrumb >>> li:first-child a:before {
    border: none;
    }
    /*
    #breadcrumb >>> li:last-child a {
    padding-right: 15px;
    -moz-border-radius: 0 4px 4px 0;
    -webkit-border-radius: 0;
    border-radius: 0 4px 4px 0;
    }
    #breadcrumb >>> li:last-child a:after {
    border: none;
    }
    */
    #breadcrumb >>> li a:before, #breadcrumb li a:after {
    content: "";
    position: absolute;
    top: 0;
    border: 0 solid #3498db;
    border-width: 20px 10px;
    width: 0;
    height: 0;
    }
    #breadcrumb >>> li a:before {
    left: -20px;
    border-left-color: transparent;
    }
    #breadcrumb >>> li a:after {
    left: 100%;
    border-color: transparent;
    border-left-color: #3498db;
    }
    #breadcrumb >>> li a:hover {
    background-color: #19be6b;
    }
    #breadcrumb >>> li a:hover:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li a:hover:after {
    border-left-color: #19be6b;
    }
    #breadcrumb >>> li a:active {
    background-color: #16a085;
    }
    #breadcrumb >>> li a:active:before {
    border-color: #16a085;
    border-left-color: transparent;
    }
    #breadcrumb >>> li a:active:after {
    border-left-color: #16a085;
    }

</style>

<template>

    <Card :style="{ width: '100%' }">

        <ul id="breadcrumb">
            <li class="active"><a href="#">Open</a></li>
            <li><a href="#">Deposit Paid</a></li>
            <li><a href="#">Job Started</a></li>
            <li><a href="#">Inspection</a></li>
            <li><a href="#">Sign Off</a></li>
            <li><a href="#">Closed</a></li>
        </ul>

    </Card>

</template>

<script>

  export default {
    
  };
  
</script>