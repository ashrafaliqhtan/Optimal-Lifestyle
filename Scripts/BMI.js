function calculateBMI() {
    // الحصول على القيم المدخلة من الحقول
    const height = parseFloat(document.getElementById('floatingHeight').value);
    const weight = parseFloat(document.getElementById('floatingWeight').value);

    // التحقق من القيم المدخلة
    if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
        document.getElementById('result').innerText = "Please enter valid height and weight values.";
        document.getElementById('result').style.color = "red";
        return;
    }

    // حساب مؤشر كتلة الجسم
    const heightInMeters = height / 100; // تحويل الطول من سنتيمترات إلى متر
    const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(2); // حساب BMI مع تقريب إلى منزلتين عشريتين

    // تحديد حالة المستخدم بناءً على مؤشر كتلة الجسم
    let status = "";
    if (bmi < 18.5) {
        status = "Underweight";
    } else if (bmi >= 18.5 && bmi < 24.9) {
        status = "Healthy Weight";
    } else if (bmi >= 25 && bmi < 29.9) {
        status = "Overweight";
    } else {
        status = "Obesity";
    }

    // عرض النتيجة
    document.getElementById('result').innerHTML = 
        `Your BMI is <strong>${bmi}</strong>, which is classified as <strong>${status}</strong>.`;
    document.getElementById('result').style.color = "green";
}
