<style>
    .arrow {
        margin: 16px;
        position: relative;
        width: 40px;
        /* Smaller width */
        height: 40px;
        /* Smaller height */
        cursor: pointer;
        transition: 0.5s;
        overflow: hidden;
    }

    .arrow:hover {
        border: solid 2px #0f8800;
        border-radius: 50%;
    }

    .arrow:after {
        position: absolute;
        display: block;
        content: "";
        color: #0f8800;
        width: 25px;
        /* Smaller width */
        height: 20px;
        /* Smaller height */
        top: -1px;
        border-bottom: solid 2px;
        transform: translatex(5px);
        /* Adjusted translation */
    }

    .arrow:before {
        position: absolute;
        display: block;
        content: "";
        color: #0f8800;
        width: 10px;
        /* Smaller width */
        height: 10px;
        /* Smaller height */
        border-top: solid 2px;
        border-left: solid 2px;
        top: 50%;
        left: 3px;
        /* Adjusted position */
        transform-origin: 0% 0%;
        transform: rotatez(-45deg);
    }

    .arrow:hover:before {
        animation: aniArrow01 1s cubic-bezier(0, 0.6, 1, 0.4) infinite 0.5s;
    }

    .arrow:hover:after {
        animation: aniArrow02 1s cubic-bezier(0, 0.6, 1, 0.4) infinite 0.5s;
    }

    @keyframes aniArrow01 {
        0% {
            transform: rotatez(-45deg) translateY(40px) translateX(40px);
            /* Smaller translation */
        }

        100% {
            transform: rotatez(-45deg) translateY(-50px) translateX(-50px);
            /* Smaller translation */
        }
    }

    @keyframes aniArrow02 {
        0% {
            transform: translateX(60px);
            /* Smaller translation */
        }

        100% {
            transform: translateX(-58px);
            /* Smaller translation */
        }
    }

    @keyframes borderAni {
        0% {
            border: solid 2px white;
        }

        100% {
            border: solid 2px white;
            border-radius: 50%;
        }
    }

    /* select row  */

    .row-selection {
        margin-bottom: 16px;
    }

    #row-select {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #f8f9fa;
        font-size: 14px;
    }
</style>

<div class="arrow no-print" id="goBackButton"></div>

<script>
    document.getElementById('goBackButton').addEventListener('click', function() {
  window.location.href = '/minireportb1';
});

</script>