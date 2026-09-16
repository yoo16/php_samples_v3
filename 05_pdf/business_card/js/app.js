function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const base64Data = e.target.result;
            document.querySelector('.card').style.backgroundImage = `url('${base64Data}')`;
            document.getElementById('bg_base64').value = base64Data;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function setText(selector, value) {
    document.querySelector(selector).textContent = value;
}

function setInfo(email, web, tel) {
    const rows = document.querySelectorAll('.info div');
    const values = [email, web, tel];

    rows.forEach((row, index) => {
        const label = row.querySelector('span');
        row.textContent = '';
        row.appendChild(label);
        row.appendChild(document.createTextNode(values[index]));
    });
}

function update() {
    const name = document.getElementById('in_name').value;
    const title = document.getElementById('in_title').value;
    const email = document.getElementById('in_email').value;
    const web = document.getElementById('in_web').value;
    const tel = document.getElementById('in_tel').value;

    const name_color = document.getElementById('in_color_name').value;
    const title_color = document.getElementById('in_color_title').value;
    const info_color = document.getElementById('in_color_info').value;

    const bgZoom = document.getElementById('in_bg_zoom').value;
    const bgX = document.getElementById('in_bg_x').value;
    const bgY = document.getElementById('in_bg_y').value;

    setText('.name', name);
    setText('.title', title);
    setInfo(email, web, tel);

    document.querySelector('.name').style.color = name_color;
    document.querySelector('.title').style.color = title_color;
    document.querySelector('.info').style.color = info_color;

    document.querySelector('.card').style.backgroundSize = `${bgZoom}%`;
    document.querySelector('.card').style.backgroundPosition = `${bgX}% ${bgY}%`;
}

document.addEventListener('DOMContentLoaded', update);
