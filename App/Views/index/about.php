<?php
global $context;
$data = $context->data;

?>
<style>
    #service-page {
        background-image: linear-gradient(rgba(15, 7, 50, 0.079), rgba(12, 3, 51, 0.084)),
            url('<?php echo url("assets/img/strips/Umdoni-Beach_strip2.jpg") ?>');
        min-height: 40vh;
        position: relative;
        background-repeat: no-repeat;
        background-size: cover;
    }


    #service-page p {
        bottom: 0px;
        position: absolute;
        font-size: 8em !important;
    }

    nav {
        width: 100%;
        position: relative;
        top: 0;
        left: 0;
        padding: 8px 1%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 20;
        background-color: #fff;
        color: #000;
    }

    nav ul li a {
        color: #000;
    }

    nav ul li i {
        color: #000;
    }
</style>


<div class="container-fluid" id="service-page">
    <div class="row">
        <div class="tag-header">
            <div class="col">
                <p class="h1 m-5 fs-1 text-white">
                    About Us
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container content-section">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="<?php echo url('assets/img/Umdoni-TownClock2.jpg') ?>"
                                    class="img-fluid rounded-start" alt="...">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">

                                    <p class="h4 text-uppercase fw-bold">
                                        OVERVIEW:
                                    </p>
                                    <p class="card-text fs-5 fw-lighter">


                                        Umdoni Local Municipality is located in KwaZulu-Natal within the Ugu District Municipality (DC21). Our local municipality consists of 19 wards.
                                        It abuts eThekwini Metro to the north, and Umzumbe to the south, making it almost halfway from Port Shepstone and Durban. 
                                        The Municipality is therefore conveniently located about 50 km from Durban and 65 km from Port Shepstone. 
                                        Umdoni has an approximate coastline of 40 km and stretches inland as far as Umzinto.

                                        <br><br>
                                        The municipality incorporates 7 traditional authority areas. The traditional Authorities fall under Ugu Local Houses of Traditional Leaders in KZN. 
                                        The Local House has its own vision, mission, and strategic focus areas, depending on the development programmes of its community. 
                                        The Ugu Local House is governed by the Traditional Leadership and Governance Framework Act, 41 of 2003, and the KZN Traditional Leadership and Governance Act, 5 of 2005. 
                                        These two pieces of legislation ensure alignment of the institution of traditional leadership in KZN with constitutional imperatives. 
                                        Umdoni Council comprises of 19 ward Councillors and 18 Proportional Representative Councillors.
                                        <br><br>
                                        Umdoni Municipality has a total population of approximately 156 443 which constitutes of 82 051 of that population being female and 74 392 being male from the total population. 
                                        The Municipality also has a total number of 33 084 of households. These statistics are based on the 2022 Census undertaken by Statistics South Africa.

                                    </p>
                                </div>
                            </div>
                        </div>
           
            </div>
        </div>
    </div>

</div>