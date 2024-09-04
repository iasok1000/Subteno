class Subteno {
    static ajax_url = '/subteno/frontend/ajax.php';
    static chat_token = "default";
    static update_his_messages_milliseconds = 1000;
    static message_id = 0;
    static first_msg_sent = false;
    static is_heart_beating = false;
    static is_chat_minimized = true;

    static update_status(element, status) {
        if (status == 'err') {
        } else if (status == 'sent') {
        }
    }
    static send_clicked() {
        let text = document.getElementById('subteno_msg_input').value;
        if (!text) {
            Subteno.error_alert("Please fill all the inputs");
            return;
        }
        let element = Subteno.add_my_message_to_template(text);
        Subteno.pushMessage(text, element);
        document.getElementById('subteno_msg_input').value = '';
    }
    static update_his_messages() {
        Subteno.is_heart_beating = false;
        Subteno.getNewMessages();
    }
    static error_alert(msg) {
        // let el1 = document.getElementById('error_alert');
        // let el2 = document.getElementById('error_alert_content');
        // el2.innerHTML = msg;
        // el1.setAttribute('hidden', false);
        // setTimeout(() => { el1.setAttribute('hidden', true); }, 2000);
        // пока продублирую а потом надо совсем убрать error_alert
        if (msg != '') {
            Subteno.add_his_message_to_template(msg, 'style="color:red;background-color:white;"');
        }
    }
    static little_thumb_clicked() {
        let el = document.getElementById('subteno_chat');
        el.classList.remove('subteno_minimized');
        let els = document.getElementsByClassName('little_thumb');
        for (let i = 0; i < els.length; i++) {
            els[i].classList.add('little_thumb_minimize');
        }
        Subteno.is_chat_minimized = false;
    }
    static subteno_button_clicked() {
        let el = document.getElementById('subteno_chat');
        el.classList.remove('subteno_minimized');
        Subteno.is_chat_minimized = false;
    }
    static minimize_clicked() {
        let el = document.getElementById('subteno_chat');
        el.classList.add('subteno_minimized');
        let els = document.getElementsByClassName('little_thumb');
        for (let i = 0; i < els.length; i++) {
            els[i].classList.remove('little_thumb_minimize');
        }
        Subteno.is_chat_minimized = true;
    }
    static add_his_message_to_template(msg, style = '') {
        let subteno_bb = document.getElementById('subteno_bottom_box');
        subteno_bb.innerHTML += '<div class="row message_row m-0"><div class="d-flex message his_message" ><div class="content px-2 rounded-2" ' + style + '>' + msg + '</div></div></div>';
        Subteno.scrollToBottom();
    }
    static my_scroll_top(el, value) {
        if (value === undefined) {
            return el.pageYOffset;
        } else {
            if (el === window || el.nodeType === 9) {
                el.scrollTo(el.pageXOffset, value);
            } else {
                el.pageYOffset = value;
            }
        }
    }
    static scrollToBottom() {
        let subteno_bb = document.getElementById('subteno_bottom_box');
        let x = subteno_bb.offsetHeight;
        let subteno_inner = document.getElementById('subteno_inner');
        Subteno.my_scroll_top(subteno_inner, x);
    }
    static add_my_message_to_template(msg) {
        let elements = '<div id="msg' + Subteno.message_id + '" class="row message_row m-0"><div class="d-flex flex-row-reverse message my_message"><div class="content px-2 rounded-2">' + msg + '</div></div></div>';
        let subteno_bb = document.getElementById('subteno_bottom_box');
        subteno_bb.innerHTML += elements;
        Subteno.scrollToBottom();
        Subteno.message_id++;
        return elements;
    }
    static async pushMessage(msg, element) {
        // console.log('pushMessage');
        let obj = {};
        obj.func = 'func_1';
        obj.token = Subteno.chat_token;
        obj.message_text = msg;
        // obj.element = element;
        // let json = JSON.stringify(obj);
        // loadAjax('/subteno/frontend/ajax.php', pushMessage_callback, "json=" + encodeURIComponent(json));
        let obj_out = await Subteno.my_fetch(obj);
        if (obj_out.hasOwnProperty('result')) {
            if (obj_out.result === "ok") {
                Subteno.update_status(element, 'sent');
                if (!Subteno.first_msg_sent) {
                    // let msg_input = document.getElementById('subteno_msg_input');
                    // msg_input.setAttribute('placeholder', 'Написать сообщение...');
                    Subteno.first_msg_sent = true;
                }
                if (!Subteno.is_heart_beating) {
                    Subteno.update_his_messages();
                }
            } else if (obj_out.result != '') {
                Subteno.error_alert(obj_out.messages);
                //     Subteno.update_status(element, 'err');
            }
        } else {
            Subteno.is_heart_beating = false;
            Subteno.error_alert("Please check you internet connection.");
        }
    }
    static async getNewMessages() {
        // console.log('getNewMessages');
        let obj = {};
        obj.func = 'func_2';
        obj.token = Subteno.chat_token;
        // console.log(obj);
        // let json = JSON.stringify(obj);
        // loadAjax('/subteno/frontend/ajax.php', getNewMessages_callback, "json=" + encodeURIComponent(json));
        let obj_out = await Subteno.my_fetch(obj);
        // console.log(obj_out);
        if (obj_out.hasOwnProperty('result')) {
            if (obj_out.result === "ok") {
                // Subteno.my_callback_get_message(obj_out.messages);
                // console.log(obj_out);
                let data = null;
                try {
                    /* if the server have error or something then this part may crash*/
                    data = JSON.parse(obj_out.messages);
                }
                catch (e) {
                    Subteno.error_alert("Error, " + e.message);
                    /* if the server had an error this updating process must continue and not stop*/
                    setTimeout(() => { Subteno.update_his_messages(); }, Subteno.update_his_messages_milliseconds);
                    return;
                }

                for (let i = 0; i < data.length; i++) {
                    var text = data[i].message_text;
                    Subteno.add_his_message_to_template(text);
                };
                if (!Subteno.is_chat_minimized) {
                    /* recursive calling is better than the timeInterval cause it will send one request at a time */
                    setTimeout(() => { Subteno.update_his_messages(); }, Subteno.update_his_messages_milliseconds);
                    Subteno.is_heart_beating = true;
                }
            } else if (obj_out.result != '') {
                Subteno.is_heart_beating = false;
                Subteno.error_alert(obj_out.messages);
            }
        } else {
            Subteno.is_heart_beating = false;
            Subteno.error_alert("Please check you internet connection.");
        }
    }
    static async my_fetch(x_in, url = Subteno.ajax_url) {
        let x_out = '{}';
        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(x_in),
            headers: {
                "Content-Type": "application/json",
            },
        });
        if (response.ok) {
            if (response.headers.get('Content-Type') === 'application/json') {
                x_out = await response.json();
            } else {
                x_out = JSON.parse(await response.text());
            }
        }
        return x_out;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#send_button').forEach(el => {
        el.addEventListener('click', Subteno.send_clicked);
    });
    document.querySelectorAll('.little_thumb').forEach(el => {
        el.addEventListener('click', Subteno.little_thumb_clicked);
    });
    document.querySelectorAll('.subteno_button').forEach(el => {
        el.addEventListener('click', Subteno.subteno_button_clicked);
    });
    document.querySelectorAll('.subteno_header').forEach(el => {
        // document.querySelectorAll('#minimize').forEach(el => {
        el.addEventListener('click', Subteno.minimize_clicked);
    });
    document.querySelectorAll('#subteno_msg_input').forEach(el => {
        el.addEventListener('keydown', (event) => {
            if (event.code === 13) {
                Subteno.send_clicked();
            }
        });
    });
});

