(function() {
    'use strict';
    window.addEventListener('load', function() {

        var forms = document.getElementsByClassName('needs-validation');

        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

function validatePhoneNumber(input) {
    const phoneNumber = input.value;
    const phoneError = document.getElementById('phoneError');

    const isValid = /^9\d{9}$/.test(phoneNumber);

    if (!isValid) {
        phoneError.style.display = 'block';
    } else {
        phoneError.style.display = 'none';
    }
}

let regionCode;
let regionId;
let provinceId;
let cityId;
let brgyId;

document.addEventListener("DOMContentLoaded", function() {

    dataGetter('https://psgc.cloud/api/regions').then( data => {
        const regionSelect = document.getElementById("region");

        data.forEach( r => {
            const option = createOption(r.name, `${r.code}-${r.id}`);

             regionSelect.append(option);
          });
    });

});

document.getElementById('region').addEventListener('change', (e)=> {

    const regionValue = e.target.value.split('-');
    regionCode = regionValue[0];
    regionId = regionValue[1];

    dataGetter(`https://psgc.cloud/api/regions/${regionCode}/provinces`).then(data=> {
        const province = document.getElementById('province');
        while(province.firstChild){
            province.removeChild(province.firstChild);
        }

        const placeholder = createOption('Select a Province', '', false);

        province.append(placeholder);
        data.forEach(p => {
            const option = createOption(p.name, p.id);
            province.append(option);
        });

    });
});


document.getElementById('province').addEventListener('change', e => {
    provinceId = e.target.value;
    dataGetter(`https://psgc.cloud/api/regions/${regionCode}/cities-municipalities`).then(data => {
        const filterCityProvince = data.filter(x => x.province_id == provinceId);

        const city = document.getElementById('city');

        while(city.firstChild){
            city.removeChild(city.firstChild);
        }

        const placeholder = createOption('Select a City', '', false);

        city.append(placeholder);

        filterCityProvince.forEach(c => {
            const option = createOption(c.name, c.id);

            city.append(option);
        });

    });
});

document.getElementById('city').addEventListener('click', e => {
    cityId = e.target.value;

    dataGetter(`https://psgc.cloud/api/regions/${regionCode}/barangays`).then(data => {
        const filterCityBaranggay = data.filter(x => x.region_id == regionId && x.province_id == provinceId && x.city_municipality_id == cityId);

        const baranggay = document.getElementById('baranggay');


        while(baranggay.firstChild){
            baranggay.removeChild(baranggay.firstChild);
        }

        const placeholder = createOption('Select a Baranggay', '', false);

        baranggay.append(placeholder);

        filterCityBaranggay.forEach(b => {
            const option = createOption(b.name, b.id);

            baranggay.append(option);
        });

    });
});
