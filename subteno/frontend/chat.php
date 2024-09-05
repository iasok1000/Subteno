<?php
?>
<link rel="stylesheet" href="/subteno/frontend/style.css">

<div class="subteno_main_holder">
    <div id="subteno_chat" class="subteno subteno_minimized">
        <div class="subteno_header">
            <div class="content">Chat with the manager</div>
            <div id="minimize" class="minimize">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.553 6.776a.5.5 0 0 1 .67-.223L8 9.44l5.776-2.888a.5.5 0 1 1 .448.894l-6 3a.5.5 0 0 1-.448 0l-6-3a.5.5 0 0 1-.223-.67z" />
                </svg>
            </div>
        </div>
        <div class="subteno_body">
            <div id="subteno_inner" class="inner scroll-style d-flex flex-column justify-content-end">
                <div id="subteno_bottom_box" class="bottom_box container"></div>
            </div>
        </div>
        <div class="input-wrapper">
            <div class="input_place">
                <div class="email_and_msg">
                    <input id="subteno_msg_input" class="bottom_text_box_input form-control" type="text" value="">
                </div>
                <button id="send_button" class="send_button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 512 512">
                        <path d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480l0-83.6c0-4 1.5-7.8 4.2-10.8L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="little_thumb">
        <svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: -0.125em;" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105" />
        </svg>
    </div>
</div>

<script src="/subteno/frontend/chat.js"></script>