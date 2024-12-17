<script setup>
// Importing Multiselect
import { getAlcoholList, getFootworkList, getPrefectureList, getRankList, getVipList } from '@/libs/utilities';
import { useQuery } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';
import { castAPI, statusAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';
import { categoryAPI } from '../../libs/api/category-api';


const initField = () => {
	name.value = "";
	email.value = "";
	password.value = "";
	point.value = 4000;
	point_rate.value = 70;
	nameReading.value = '';
	nickname.value = "新規会員";
	nearestStation.value = '';
	manager.value = 1;
	referralSource.value = '';
	registrationDate.value = new Date().toISOString().split('T')[0];
	postalCode.value = '';
	prefecture.value = '';
	city.value = '';
	townName.value = '';
	streetAddress.value = '';
	rank.value = '';
	footwork.value = '';
	alcoholTolerance.value = '';
	dayJob.value = '';
	nightJob.value = '';
	instagramId.value = '';
	height.value = '';
	bust.value = '';
	waist.value = '';
	hip.value = '';
	educationalBackground.value = '';
	vipService.value = '';
	castStatus.value = '';
	dateOfBirth.value = '2004-01-01';
	presidentConfirmation.value = '';
	category.value = 1;
	phoneNumber.value = '';
	lineId.value = '';
	featureTags.value = '';
	managementTags.value = '';
	otherFeatures.value = '';
	fetchCategories();	
};

const name = ref('');
const email = ref('');
const password = ref('');
const point = ref(0);
const point_rate = ref(0);
const nameReading = ref('');
const nickname = ref('');
const nearestStation = ref('');
const manager = ref('');
const referralSource = ref('');
const registrationDate = ref('');
const postalCode = ref('');
const prefecture = ref('');
const city = ref('');
const townName = ref('');
const streetAddress = ref('');
const rank = ref('');
const footwork = ref('');
const alcoholTolerance = ref('');
const dayJob = ref('');
const nightJob = ref('');
const instagramId = ref('');
const height = ref('');
const bust = ref('');
const waist = ref('');
const hip = ref('');
const educationalBackground = ref('');
const vipService = ref('');
const castStatus = ref('');
const dateOfBirth = ref('');
const presidentConfirmation = ref('');
const category = ref(1);
const phoneNumber = ref('');
const lineId = ref('');
const categoryOptions = ref([]);
const featureTags = ref([]);
const managementTags = ref([]);
const otherFeatures = ref('');

const managerOptions = ref([
	{ name: '仮担当登録', id: '1' },
	{ name: '担当2', id: '2' },
	{ name: '担当3', id: '3' },
]);

const referralSourceOptions = ref([
	{ name: '紹介者1', id: '1' },
	{ name: '紹介者2', id: '2' },
	{ name: '紹介者3', id: '3' },
]);
const prefectureOptions = ref(getPrefectureList());
const rankOptions = ref(getRankList());
const footworkOptions = ref(getFootworkList());
const alcoholToleranceOptions = ref(getAlcoholList());
const vipServiceOptions = ref(getVipList());


const castStatusOptions = ref([
	{ label: '稼働', value: '100' },
	{ label: '一時停止', value: '101' },
	{ label: '強制停止', value: '102' },
	{ label: '削除済み', value: '103' },
	// { label: '待機', value: '100' },
	// { label: '合流中', value: '2' },
	// { label: '業務外', value: '3' },
]);

const presidentConfirmationOptions = ref([
	{ label: '確認済', value: true },
	{ label: '未確認', value: false },
]);


// const generateEmail = () => {
// const randomTwoDigitNumber = Math.floor(Math.random() * 90) + 10; // Generates a number between 10 and 99
// return `${name.value}${nameReading.value}${randomTwoDigitNumber}@gmail.com`;
// };

const handleSubmit = async () => {
	Swal.fire({
		title: "登録を実行します。よろしいですか？",
		showCancelButton: true,
		confirmButtonText: "はい",
		cancelButtonText: `いいえ`
	}).then(async (result) => {
		/* Read more about isConfirmed, isDenied below */
		if (result.isConfirmed) {
			try {
				const castData = {
					name_ja: name.value,
					name_kana: nameReading.value,
					nickname: nickname.value,
					city: city.value,
					person_in_charge_id: manager.value, // Assuming manager is an ID
					registered_date: registrationDate.value,
					status_id: castStatus.value, // Assuming castStatus is an ID
					rank: rank.value,
					post_code: postalCode.value,
					prefecture: prefecture.value,
					municipalitie: townName.value,
					street: streetAddress.value,
					building: '', // Add building if applicable
					station: nearestStation.value,
					footwork: footwork.value,
					alcohol: alcoholTolerance.value,
					day_work: dayJob.value,
					night_work: nightJob.value,
					height: height.value,
					three_size_b: bust.value,
					three_size_w: waist.value,
					three_size_h: hip.value,
					vip_status: vipService.value,
					birthday: dateOfBirth.value,
					ceo_check: presidentConfirmation.value === 'Option1', // Assuming 'Option1' means true
					instagram_id: instagramId.value,
					category_id: category.value, // Assuming category is an ID
					tag_of_spec: featureTags.value,
					notices: otherFeatures.value,
					email: email.value, // Add email if applicable
					password: password.value, // Add password if applicable
					basic_point_price: point.value,
					point_rate: point_rate.value,
					status: 100,
					live_status: 120,
					work_status: 110,
					line_id: lineId.value,
					phone_number: phoneNumber.value,
				};
				// 送信処理を追加する
				const response = await castAPI.create(castData);
				Swal.fire({
					icon: 'success',
					title: '登録成功',
					text: 'お客様の登録が成功しました！',
					confirmButtonText: 'OK',
				});
				initField();
			} catch (error) {
				//alert('submit failed:' + error);
				// エラーハンドリングを追加する
				const errorMessage = JSON.parse(error.message);
				let errorHtml = '';
				for (const [key, value] of Object.entries(errorMessage)) {
					errorHtml += `<p><strong>${key}:</strong> ${value[0]}</p>`;
				}

				Swal.fire({
					icon: 'error',
					title: '登録失敗',
					tegxt: 'エラーが発生しました。詳細はコンソールをご確認ください。',
					html: errorHtml, // Display the error details in the footer
					confirmButtonText: 'OK',
				});
			}
		}
	});


};

const allFeatureTags = [
	'童顔',
	'ナチュラル',
	'可愛い系',
	'綺麗系',
	'ギャル',
	'清楚',
	'モデル系',
	'アイドル系',
	'ハーフ',
	'プライベート',
	'接待',
	'わいわい',
	'しっとり',
	'カラオケ',
	'タバコNG',
	'マナー重視',
	'誕生日会',
	'ゴルフ',
	'ご飯',
	'旅行',
	'盛り上げ上手',
	'歌上手',
	'お酒好き',
	'英語が話せる',
	'中国語が話せる',
	'韓国語が話せる',
];

// 管理タグ
// 悪い
// 　遅刻
// 　酒癖
// 　無断欠勤あり
// 　セクハラNG
// 　クレーム歴あり
// 　非協力的

// 良い
// 　新人
// 　優良
// 　急な対応可
// 　協力的
// 　セクハラ耐性あり
// 　アフターOK

const allManagementTags = [
	'遅刻',
	'酒癖',
	'無断欠勤あり',
	'セクハラNG',
	'クレーム歴あり',
	'非協力的',
	'新人',
	'優良',
	'急な対応可',
	'協力的',
	'セクハラ耐性あり',
	'アフターOK',
];

// ファイルアップロード

// 画像を選択ボタンを押してアップロードする機能
// const uploadedImages = ref([]);
// const fileInput = ref(null);

// const triggerFileInput = () => {
//     fileInput.value.click();
// };

// const handleFileUpload = (event) => {
//     const files = event.target.files;
//     for (let i = 0; i < files.length; i++) {
//         const file = files[i];
//         const reader = new FileReader();
//         reader.onload = (e) => {
//             uploadedImages.value.push({
//                 file: file,
//                 url: e.target.result
//             });
//         };
//         reader.readAsDataURL(file);
//     }
// };

// const removeImage = (index) => {
//     uploadedImages.value.splice(index, 1);
// };

// ダミーデータを表示しておく機能
const fileInput = ref(null);
const dummyImages = ref([1, 2, 3, 4]);
const dummyImageSrc =
	'data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAARkAAAD7CAYAAABe6+AqAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAABhaVRYdFNuaXBNZXRhZGF0YQAAAAAAeyJjbGlwUG9pbnRzIjpbeyJ4IjowLCJ5IjowfSx7IngiOjI4MSwieSI6MH0seyJ4IjoyODEsInkiOjI1MX0seyJ4IjowLCJ5IjoyNTF9XX0WrjWxAAAR9UlEQVR4Xu3dh5LbOBpFYXk8Oeecw3PNq+yT+Ukm55xz8tbh6vdqtSQItXjVlH2+Kpbc6hYFgcQlAJLylWvXrl3fSFLIbdtHSYowZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKSoK9euXbu+/fdqXb16dXP//fdv7rnnnu0zy/rpp5+GRdLyziJk7rrrrs3zzz+/efLJJ7fPLOvDDz/cfPTRR9ufJC3pLIZLV65c2dxxxx2bu+++O7Lcfvvt23eStDTnZCRFGTKSogwZSVFnMfF75513bp5++unN448/vn3mv2677bZhYpi/afntt982v//++/an//XJJ59sPvvss+1PkpZ0FiFDkDBBS5jsqwB69NFHt8+MI0SmguTXX38dQkjS8s4iZFoIn5dffnnzzDPPbJ8Z9+67727ee++97U+STsWQCavhHAun4fn577//3vz111/D8I3ln3/+2f51H07p04OrpU7B1zr/+OOPzZ9//rm5fv34TUt5WX8tXBjJI8+PoQy8P4+1LFGOKdRp1e1uuRJ1cRHUF9tod/uz/VgoEwvbn3JWeQ/dH9bOkJnBVcb33XffsLNM4Wrhn3/+efvTf7DDc5Xygw8+uLn33nuH9bCTsZ5qfAzRfvjhh8133303DNnmdi52UNbDOll3DSFZL2hMrJPlxx9/3Hz//fcHDwOr0dZCA+G5WipkpuqDMtBY+Hz8m8/1yy+/DI88v1TwUQ8PPPDAsG34d4Vtlavqgvfk/dlGPPI8f1Ovm0J52aYcEA61Wz4e2U4sVX/7IcN7EC41bKesbD+euxkYMjOeeOKJzQsvvDA0uCm7Vwyz8xAqjz322LCwo001SLAjffvtt5vPP/98CJuxnZp18jkfeeSRYXnooYeGRsXzY9hx2WG//vrrYb3stL2Nm8l15riqYdBwp96nB426Gjmfk+A7pvFQpocffniYg+OxVQ+FMvD+vDdloC7m5vG++OKLYZsSUr0oB6HCNqJsc9tpDAcawo1yfvPNN0PYENjn7Oqbb775r+2/zxKNgA1KY26hAbMcil4DDa8a3NhSvRGOVOxgzz333LATc7Ss7vuUOqqyfhrf/tGe37OzPvvss0OQ8m/es7Xj8jvKQm+HRwKnt2Fz6wblJ1TrqHsM1sFnq14H9UFZLtJwaMDUAeVjm8/VQ6EM1bPgAMDC63ludzvuLgQjodRbTt6DdVI2ysh+w3oOrT/+nrqvstZQ6iI9qrXwOpmF0JAIFnpVNFQady92LMKDHhOPtWNyFCTgXnrppRu9i0Ow49ObojfGui4Tn4nQ4x40ApMG3ovX0uioH15L46s6OkQFAdvn0LpsoW6pY7b9U089tUhdE8a1T6xh+x3DkFlANR4WGsNFGgCvYadiJ6UB1FH7xRdfHBrGXI9oCkfTGratAY2FwCQ8KVsP6oNw4XVLNDbq+iLbaEzVL9tp9wCxjx7J7lwZvV9+npuHo/fHZ6eHTEieI4dLMwgQNnCrQVT39tgGwA5KD4hAobtdR/xjGwTrZBjGDj63U1OXfN5d1WVnCME6WJg3qIXhGPMeDPMo+1wg0lhYWN/cxDT1Ts+DwO3tHTK0oDyUmfXzM5+BeuwNa+Zw2F9awyXWRV3R25ja/3hv1vXll19uvvrqq2GerOamqEfKR7n4nFNl43NXfR0yR7QWTvzO4Oj5yiuvHNS9ZgdnZ6hxNDsQQdQKql28HruNinWxzvodOx3r7G147OR8fnb4FuqSz1vBshskvP/u5yo0DspBHXHkJah4bB15Wf/777+/+eCDD5rBRyOmPPQSWgg4ylYBWPM+lJVyUPccBBhq1Zm5VuB8+umnQ321QpBgob7oyYwdCHh/AoV1Tc3vUC4+21xvhddSV0xG79f/2tmTmdHTk0Ht5JwRqKMWOxjvSdeYRkpjYj2txgd+z8I6aSzsoKyThfXXOjmyscOxzp7yETC8poVGyHtzxOX9OMvCY32Gary7C8FHY2T9dXSmQdOQp3phPE998RrWMaaGVsxJTK0H1CvvS2Nmoe4pL2HD5+WR3/Mc78ff00NsBTR/1+rJUN8MbVnGtif1zfbnzCPrmQpSnqe+qEPKNHUwIxDZ1hWg58SQmdETMmx0dqi6dYEGWkd/Fv5dOz07NjtT6ygK1kl5aeSsk4bOjl/rpPHUOikb62yFF42UhsbSwk7PeitYKAcNplc1Gh4JLHpbU2hYlIewGcMRnpDhs02hbJSTxkyZWddUeXme96QuGI62eqdzIcP+Ru+ZHtsY6oB74jgo9NQf9cy+wf42tR15nu1O2c5J3wBVTeyMdK05irITjO1UNDoaFA1hrjeBWufHH388vI7X7+N9CBmCiMcWgqgVlIX1HHLqdgzlovysp6VVJkKYBjfViAuNmRCmMZ9qGEFjJwBbBzY+O3XQEzCg7IRla9+gZzd3MFkjQ2YBNEyOLj07FH/XcyRinVPhsq/ev4Wjd2vIsTR6DHO9oFbIVI+vFYysmx4kS089LYXeGQE4VbYa1kz10KYQmPRSp7D9eO9jTzCcmiFzYjQ8liXRoFnWht5Qq0fUCj4aU2uoBeqRoz+N85QYZrWGcJSrNWyb0rNvEL6tuaQ1MmROjCNu4qg716CXVGeTGMowbODyfE4zM3/CtUJcM1LX91y090TAtOZMwNCiZ+i5NMrVKttFQ58e0NyQj96TwyXN6tmZDpUMGXZsAoUw4VQrAcJVyJy+rVPer7766rC89tprNxaCpzXcaaEhzTUmjvqn7sERmgxXWr0JfsfFhlw/c+gyd6qe+rxonV4WQ+YSnLLXcVH0Vjg7RFCw81eYEB488hy/I3g4U8Pwgd7H3FmzXj0hcxn1SAMnRFo9NOqCe5hef/31g5fWTZvoqZe1MWQuAWP1Q8frp0RYECD0Vuid8MjPddPfKfQcsS8jZM6xkV82Q0Y3cHSm98KQiJ5LXaNy6LwKc04MY44JAN5z7n1T81taliGjGwiY+qqC1tmTQohwqpZTyFxly8VnXBTHwi0Dc9fJtPQECEOzpYZnynELacDQhMlKhkWtU8c0fIKFL8OqMOG+sHfeeWfz9ttvb956663h3/yO08sX1TMU6hlSXQZOX3OBZAXukgtBTv2fE0NGA+ZbuNGvdaEXQyB6LFyJTJAQMIQNPRZ2/CXnR3p6MpcRMj3zaZz14ipkAnfphTqfuzVkbQwZDROZhEzrMnkaPJfuEzAcpbkALjl53RMynOVpnUpO6OlhXUa51syQ0TA84jqY1vwGF71x39Wpuuo9F7RxQVxraJfA9U30VFrXOREw9AidL/oPa0FDg5i7H4ZwOeVcQM8l9oQMva9T9xqYc2ndl0TPkHubTh2Aa2XIqGtuo2f4siQa8dyQjJ4CX8PBBWy9167wGgL1mLkcyjV3vxThxxD00NP/LayL8i+5zlMwZNR1gVnPdStLYt6DIdrckIkeA6fcW98qBxonp+X5Aiy+aKrnFP0UAoZeXSt0670o3xL1Rm+NMO35ArW1MWTUNZnJ0f+QYcncTYQ9+PqKuTMpNGBuxOR+Kq7xqS9Np5EzXOHaHxomFxbW/Vacqu/t+YxhGMcZtbmvZSAUKBPzXRcNGspJj4j1cDvHsWW/DGcRMnUUorL3F44Uc/MJYIcbez3LrT527plkpaFQV3M7ONuKbUKjoHEfg94C19rMBSANmBsLCRBug+CRQCF46p4rFno8hM4SKNfc99hQV4Qe93kROOynvWFDoBOedfX1Mf8TxmU7i6/fpMK5SIydhIrfX2gAc11INjg7/9jrOVMwNanJa+a6qHwDGkuvta2TYGAHZh1TWO/uuvevF+F3u3dqM1SYC2/Cg/JMfeFWvQcHGHpFcw2Mz0FDJkhqToTPxGsPPfpTJso2FXB1donP3PqcvC9/w0LZWKgryspS9co+znpqO9Jjqe835rkq/1y51uisQoaFHWh3YQfa3fmnsI7919ZSG27MMY13ytrWydGYeqRhthpj/U3VGw2neoOEC9uH+Q5+pgGhFQxzIYPqYfFebMNj1Slowqv1WXsac539oi5aZaMOdnvSFeg80gNj4WBHr4dQoR6pz7FwPMeQcU5GQ8iw4zLPsNs7GUNj4SjLUISvJnjjjTeGheEJz/N71sFcSis8elE2hiV8f3JrDqQHocC1PlylPLcuwqh1LQz4fd2z1VO2ChtChTCu/xCQheEl4UIILRGma2LIaMBwkQZzbEOm4REK/B9BPC6B09kEA1+qzlzIXOPfx9/zOsKAy/LHenP76Cn09BaqbPx/SL3fyXyrMWQ0oHFw28BFewz0XngdDY5bDwisQ8OghcbM/UDcjMkjDZqhVKvnRUjwd/U6goDT4gwp54bYrHeuV1coG/VWnztxywXbh/VSx0vW6ykYMrph96jM0Kl3Z6axE1DcJVxf8bB0I0O9T935TW+Jxs0QiOd5X3op/L9XBAvl4e/4e35f8zs16bok6or3rXJRjwwXjwkEXkuoUHZ6YayXEDun+RicxX9TyxiViTDGqwnsHGzIMbwnE3KtI1/r9WPWvk4mG1kfdc4E5dgEJEdWQomFoRZDIxrV7nCB11OmKTQWynTMV0Kwb1TPhH/z/oRJLWNhV9ecTM190LgJC0Lqomqit+qvzirxnvuT4lXGKjv1wiN1S8hQr9TxMYF1mc7+/8JWDsFSZ5H2w6uOsix1tuYc8Dm47oRrV6bQoAkZekhLIFgqaFgqsPdDhjqlLlkImKmQPDeGjG4pnComZDjdP4XeFSEzd7Wx+jgno9VjeMHRv3XNTQ96ETUEnELPgQnWugZGxzNktHrMb9SNjcxz8PMhE7f8LUM+rhhnPVNzMWCYQg/GkFmOwyWtHkMcJmrr6mzmTOht1LwFC/MZu/MXBEtNtHJbAhfAMUSiN9PCPAxDJd5DyzBktHoVMjwWzsTQ26iw4YzM7pktAoZAoddDyOyfHRvDuuq0+O66dBxDRqs3FjJLozfEtTVcI0TYaDnOyeiWV9frcAGdAbM8Q0a3LOZwuM2AHgz3RS1xQ6f+nyGjWw7zLVxEyCX6zMGwcNXxzXDh2xo5J6PVY+KWr5Hg+1c4W8Qkbj0ywTs3qVuX67MwWUzAcJqa+5wYHhkuWYaMzgbBwtmi3UeW3VseOHXNRXsES4UHp7c5A8VCwBAs53aT4TkzZHRTIWRY9q+b0eVxTkY3FXow9FIMmPUwZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJUYaMpChDRlKUISMpypCRFGXISIoyZCRFGTKSogwZSVGGjKQoQ0ZSlCEjKcqQkRRlyEiKMmQkRRkykqIMGUlRhoykKENGUpQhIynKkJEUZchIijJkJEUZMpKiDBlJQZvNvwG7ALaCzBxD7wAAAABJRU5ErkJggg==';

const triggerFileInput = () => {
	fileInput.value.click();
};

const handleFileUpload = (event) => {
	console.log('File upload triggered');
};

const removeImage = (index) => {
	dummyImages.value.splice(index, 1);
};

const fetchCategories = async () => {
	try {
		const response = await categoryAPI.getAll();
		console.log('response', response);
		categoryOptions.value = response.data;
	} catch (error) {
		console.log('Error fetching categories:', error);
	}
};
const fetchAddress = async () => {
	if (postalCode.value.length === 7) {
		try {
			const address = await castAPI.fetchAddressByPostalCode(postalCode.value);
			if (address) {
				prefecture.value = address.prefecture;
				city.value = address.city;
				townName.value = address.townName;
			} else {
				console.warn('住所が見つかりませんでした');
			}
		} catch (error) {
			console.error('住所情報の取得に失敗しました', error);
		}
	}
};

const { data: statusByCategory } = useQuery({
	queryKey: [VueQuery.GET_STATUS_BY_CATEGORY],
	queryFn: () =>
		statusAPI.getStatusesByCategory('cast').then((response) => {
			return response.data.cast;
		}),
});

onMounted(async () => {
	initField();
	await fetchCategories();
});
</script>

<template>
	<div>
		<form @submit.prevent="handleSubmit">
			<div class="container pb-0 pt-0">
				<div
					class="d-flex justify-content-between align-items-center content-header ps-0 pe-0"
				>
					<h4>女性会員登録</h4>
					<button type="submit" class="register-btn btn">保存</button>
				</div>
			</div>
			<div class="container pt-0">
				<div class="card card-dark">
					<div class="card-header">
						<h4 class="fs-3 fw-normal">基本情報</h4>
					</div>

					<div class="card-body fs-5">
						<div class="register-area">
							<!-- Upper part from hr -->
							<div class="upper-area">
								<div class="main-area">
									<!-- 1st row -->
									<div class="d-flex align-items-end register-input">
										<div>
											<label for="name" class="required">*名前</label>
											<input
												type="text"
												v-model="name"
												id="name"
												class="form-control"
												placeholder="名前を入力"
											/>
										</div>
										<div>
											<label for="nameReading">ふりがな</label>
											<input
												type="text"
												v-model="nameReading"
												id="nameReading"
												class="form-control"
												placeholder="ふりがなを入力"
											/>
										</div>
										<div>
											<label for="nickname" class="required"
												>*ニックネーム</label
											>
											<input
												type="text"
												v-model="nickname"
												id="nickname"
												class="form-control"
												placeholder="ニックネームを入力"
											/>
										</div>
										<div>
											<label for="nearestStation">最寄り駅</label>
											<input
												type="text"
												v-model="nearestStation"
												id="nearestStation"
												class="form-control"
												placeholder="駅名を入力"
											/>
										</div>
									</div>

									<!-- 2nd row -->
									<div class="d-flex align-items-end register-input second-row">
										<div class="select-wrapper">
											<label for="referralSource">紹介元</label>
											<!-- <select v-model="referralSource" id="referralSource"
                                                class="form-select referralSource-input">
                                                <option disabled value="">紹介者選択</option>
                                                <option v-for="option in referralSourceOptions" :key="option"
                                                    :value="option">{{ option }}</option>
                                            </select> -->
											<multiselect
												v-model="referralSource"
												:options="referralSourceOptions"
												label="name"
												:searchable="true"
												placeholder="紹介者選択"
												track-by="id"
												style="min-width: 230px"
											/>
										</div>
										<div class="select-wrapper">
											<label for="manager" class="required">*担当</label>
											<select
												v-model="manager"
												id="manager"
												class="form-select manager-input fs-4"
											>
												<option disabled value="">担当選択</option>
												<option
													v-for="option in managerOptions"
													:key="option.name"
													:value="option.id"
												>
													{{ option.name }}
												</option>
											</select>
										</div>
										<div>
											<label for="registrationDate">登録日</label>
											<input
												type="date"
												v-model="registrationDate"
												id="registrationDate"
												class="form-control"
											/>
										</div>
									</div>

									<!-- 3rd row -->
									<div class="d-flex align-items-end register-input">
										<div>
											<label for="postalCode">郵便番号(ハイフン無し)</label>
											<input
												type="text"
												v-model="postalCode"
												id="postalCode"
												class="form-control"
												placeholder="1234567"
												@blur="fetchAddress"
											/>
										</div>
										<div>
											<label for="prefecture">都道府県</label>
											<select
												v-model="prefecture"
												id="prefecture"
												class="form-select prefecture-input fs-4"
											>
												<option disabled value="">選択</option>
												<option
													v-for="option in prefectureOptions"
													:key="option"
													:value="option"
												>
													{{ option }}
												</option>
											</select>
										</div>
										<div>
											<label for="city">市区町村</label>
											<input
												type="text"
												v-model="city"
												id="city"
												class="form-control"
												placeholder="市区町村"
											/>
										</div>
										<div>
											<label for="townName">町名など</label>
											<input
												type="text"
												v-model="townName"
												id="townName"
												class="form-control"
												placeholder="町名など"
											/>
										</div>
										<div>
											<label for="streetAddress">番地以降</label>
											<input
												type="text"
												v-model="streetAddress"
												id="streetAddress"
												class="form-control"
												placeholder="番地以降"
											/>
										</div>
										<div>
											<label for="phoneNumber">TEL</label>
											<input
												type="tel"
												v-model="phoneNumber"
												id="phoneNumber"
												class="form-control"
												placeholder="電話番号"
											/>
										</div>
									</div>

									<div class="d-flex align-items-end register-input">
										<div>
											<label for="height" class="required">*身長</label>
											<input
												type="number"
												v-model="height"
												id="height"
												class="form-control"
												placeholder="身長を入力"
											/>
										</div>
										<div>
											<label for="bust" class="">スリーサイズ</label>
											<div class="d-flex align-items-center">
												<input
													type="number"
													v-model="bust"
													placeholder="B"
													id="bust"
													class="form-control"
												/>
												<span class="mx-2"> - </span>
												<input
													type="number"
													v-model="waist"
													placeholder="W"
													class="form-control"
												/>
												<span class="mx-2"> - </span>
												<input
													type="number"
													v-model="hip"
													placeholder="H"
													class="form-control"
												/>
											</div>
										</div>
									</div>

									<!-- 4th row -->
									<div class="d-flex align-items-end register-input">
										<div>
											<label for="educationalBackground">最終学歴</label>
											<input
												type="text"
												v-model="educationalBackground"
												id="educationalBackground"
												class="form-control"
												placeholder="最終学歴を入力"
											/>
										</div>
										<div>
											<label for="dateOfBirth" class="required"
												>*生年月日</label
											>
											<input
												type="date"
												v-model="dateOfBirth"
												id="dateOfBirth"
												class="form-control"
											/>
										</div>
										<div>
											<label for="rank" class="required">*クラス</label>
											<select
												v-model="rank"
												name=""
												id="rank"
												class="form-select rank-input fs-4"
											>
												<option disabled value="">クラス選択</option>
												<option
													v-for="option in rankOptions"
													:key="option.label"
													:value="option.value"
												>
													{{ option.label }}
												</option>
											</select>
										</div>
										<div>
											<label for="footwork" class="required"
												>*フットワーク</label
											>
											<select
												v-model="footwork"
												name=""
												id="footwork"
												class="form-select footwork-input fs-4"
											>
												<option disabled value="">選択</option>
												<option
													v-for="option in footworkOptions"
													:key="option.label"
													:value="option.value"
												>
													{{ option.label }}
												</option>
											</select>
										</div>
										<div>
											<label for="alcoholTolerance" class="required">*酒</label>
											<select
												v-model="alcoholTolerance"
												name=""
												id="alcoholTolerance"
												class="form-select alcoholTolerance-input fs-4"
											>
												<option disabled value="">選択</option>
												<option
													v-for="option in alcoholToleranceOptions"
													:key="option.label"
													:value="option.value"
												>
													{{ option.label }}
												</option>
											</select>
										</div>
									</div>

									<div class="d-flex align-items-end register-input">
										<div>
											<label for="dayJob">昼職</label>
											<input
												type="text"
												v-model="dayJob"
												id="dayJob"
												class="form-control"
												placeholder="職業を入力"
											/>
										</div>
										<div>
											<label for="nightJob">夜職</label>
											<input
												type="text"
												v-model="nightJob"
												id="nightJob"
												class="form-control"
												placeholder="職業を入力"
											/>
										</div>
										<div>
											<label for="lineId">LineID</label>
											<input
												type="text"
												v-model="lineId"
												id="lineId"
												class="form-control"
												placeholder="LINEのIDを入力"
											/>
										</div>
										<div>
											<label for="instagramId">インスタID</label>
											<input
												type="text"
												v-model="instagramId"
												id="instagramId"
												class="form-control"
												placeholder="インスタのIDを入力"
											/>
										</div>
										<div>
											<label for="vipService">VIP対応</label>
											<select
												v-model="vipService"
												name=""
												id="vipService"
												class="form-select vip-input fs-4"
											>
												<option disabled value="">選択</option>
												<option
													v-for="option in vipServiceOptions"
													:key="option.label"
													:value="option.value"
												>
													{{ option.label }}
												</option>
											</select>
										</div>
									</div>

									<!-- 5nd row -->
									<div class="d-flex align-items-end register-input">
										<div>
											<label for="email" class="required">*email</label>
											<input
												type="text"
												v-model="email"
												id="email"
												class="form-control"
												placeholder="email"
											/>
										</div>
										<div>
											<label for="password" class="required">*password</label>
											<input
												type="password"
												v-model="password"
												id="password"
												class="form-control"
												autocomplete="new-password"
												placeholder="password"
											/>
										</div>
										<div>
											<label for="point" class="required"
												>*Deet報酬ポイント</label
											>
											<input
												type="number"
												v-model="point"
												id="point"
												class="form-control"
											/>
										</div>
										<div>
											<label for="point" class="required"
												>*ポイント換金レート</label
											>
											<input
												type="number"
												v-model="point_rate"
												id="point_rate"
												class="form-control"
											/>
										</div>
									</div>
								</div>

								<!-- side area -->
								<div class="status-area">
									<div>
										<label for="castStatus">稼働ステータス</label>
										<!-- <select v-model="castStatus" name="" id="castStatus"
											class="form-select register-input fs-4">
											<option disabled value="">稼働状況を選択</option>
											<option v-for="option in castStatusOptions" :key="option.label"
												:value="option.value">
												{{ option.label }}
											</option>
										</select> -->
										<select
											v-model="castStatus"
											name="statusByCategory"
											id="castStatus"
											class="form-select register-input fs-4"
										>
											<option disabled value="">稼働状況を選択</option>
											<option
												v-for="option in statusByCategory"
												:key="option.id"
												:value="option.id"
											>
												{{ option.status_name }}
											</option>
										</select>
									</div>
									<div>
										<label for="presidentConfirmation">社長確認</label>
										<select
											v-model="presidentConfirmation"
											name=""
											id="presidentConfirmation"
											class="form-select register-input fs-4"
										>
											<option disabled value="">選択</option>
											<option
												v-for="option in presidentConfirmationOptions"
												:key="option.label"
												:value="option.value"
											>
												{{ option.label }}
											</option>
										</select>
									</div>
								</div>
							</div>

							<hr />

							<!-- Lower part from hr -->
							<div>
								<label for="category" class="required">*カテゴリ</label>
								<select
									v-model="category"
									name=""
									id="category"
									class="form-select register-input tag-input fs-4"
								>
									<option disabled value="">カテゴリ選択</option>
									<option
										v-for="option in categoryOptions"
										:key="option.category_name"
										:value="option.id"
									>
										{{ option.category_name }}
									</option>
								</select>
							</div>

							<SiteadminTagSelector
								v-model:selectedTags="featureTags"
								:allTags="allFeatureTags"
								label="特徴タグ"
							/>

							<SiteadminTagSelector
								v-model:selectedTags="managementTags"
								:allTags="allManagementTags"
								label="管理タグ"
							/>

							<div>
								<label for="otherFeatures">その他特徴</label>
								<textarea
									v-model="otherFeatures"
									name=""
									id="otherFeatures"
									class="form-control register-input"
									style="height: 15rem"
								></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- file upload -->
			<div class="container">
				<div class="card card-dark">
					<div class="card-header">
						<h4 class="fs-3 fw-normal">画像</h4>
					</div>
					<div class="card-body">
						<div class="register-area">
							<div>
								<input
									type="file"
									ref="fileInput"
									class="file-input"
									@change="handleFileUpload"
									accept="image/*"
									multiple
								/>
								<button
									class="image-upload-btn"
									@click="triggerFileInput"
									type="button"
								>
									画像を選択
								</button>
							</div>
							<hr />

							<div class="d-flex flex-wrap">
								<!-- ダミーデータ -->
								<div
									v-for="(image, index) in dummyImages"
									:key="index"
									class="image-container position-relative"
								>
									<img
										:src="dummyImageSrc"
										alt="Dummy image"
										class="img-thumbnail uploaded-image"
									/>
									<button
										@click="removeImage(index)"
										class="delete-button"
										aria-label="画像を削除"
									>
										<span class="delete-icon">×</span>
									</button>
								</div>
								<!-- <div v-for="(image, index) in uploadedImages" :key="index"
                                    class="image-container position-relative">
                                    <img :src="image.url" alt="Uploaded image" class="img-thumbnail uploaded-image">
                                    <button @click="removeImage(index)" class="delete-button" aria-label="画像を削除">
                                        <span class="delete-icon">×</span>
                                    </button>
                                </div> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<style scoped>
.required {
	color: red;
}
.container {
	padding: 40px;
	margin: 0px auto;
	max-width: 1620px;
	width: 100%;
}

.content-header {
	padding: 11px 24px;
	background: #ffffff 0% 0% no-repeat padding-box;
}

.content-header h4 {
	font: normal normal normal 16px/20px Meiryo UI;
	margin: 0;
	color: #4f5d73;
}

.card {
	border: 1px solid #e0e0e0;
	border-radius: 4px;
}

.card-header {
	background: #f7f7f7 0% 0% no-repeat padding-box;
	padding: 14px 22px;
}

.card-body {
	padding: 27px 28px 27px 35px;
	font-weight: 400;
	color: #3c4b64;
	font-family: Meiryo UI;
}

.card-header h4 {
	font: normal normal bold 14px/18px Meiryo UI;
	color: #3c4b64;
	margin: 0;
}

.register-btn {
	background: #20a8d8;
	border: none;
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
	align-items: center;
	padding: 6px 14px;
}

.register-btn:hover {
	background: #1a8ab3;
	color: #ffffff;
}

.card-dark .card-header {
	background: #2f353a 0% 0% no-repeat padding-box;
}

.card-dark .card-header h4 {
	color: #ffffff;
}

.upper-area {
	display: flex;
}

.register-input {
	margin-bottom: 24px;
}

.register-input > div {
	margin-right: 14px;
}

.register-input > div:last-child {
	margin-right: 0;
}

.referralSource-input {
	min-width: 130px;
}

.manager-input {
	min-width: 114px;
}

.prefecture-input {
	min-width: 82px;
}

.rank-input {
	min-width: 114px;
}

.footwork-input {
	min-width: 82px;
}

.alcoholTolerance-input {
	min-width: 82px;
}

.vip-input {
	min-width: 82px;
}

.status-area {
	margin-left: 60px;
	max-width: 163px;
	width: 100%;
}

hr {
	margin-bottom: 40px;
}

.tag-input {
	max-width: 1070px;
}

/* file upload */
.file-input {
	display: none;
}

.image-upload-btn {
	background: #2f353a;
	border: none;
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
	align-items: center;
	padding: 6px 14px;
	border-radius: 3px;
}

.image-upload-btn:hover {
	background: #44494e;
	color: #ffffff;
}

.image-container {
	width: 182px;
	height: 182px;
	margin-right: 40px;
	margin-bottom: 20px;
}

.uploaded-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.delete-button {
	position: absolute;
	top: -10px;
	right: -10px;
	width: 23px;
	height: 23px;
	border-radius: 50%;
	background-color: white;
	border: 2px solid #707070;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 0;
	cursor: pointer;
}

.delete-icon {
	color: #707070;
	font-size: 18px;
	font-weight: bold;
	line-height: 1;
}

.delete-button:hover {
	background-color: #f8f9fa;
}

.form-select,
.form-control {
	height: 43px;
}

@media (max-width: 768px) {
	.container {
		padding: 20px;
	}

	.register-area {
		flex-direction: column;
	}

	.upper-area {
		display: block;
	}

	.main-area,
	.status-area {
		width: 100%;
		max-width: 100%;
		margin-left: 0;
	}

	.register-input {
		flex-direction: column;
	}

	.register-input > div {
		width: 100%;
		margin-right: 0;
		margin-bottom: 15px;
	}

	.select-wrapper,
	.form-control,
	.form-select {
		width: 100%;
	}
}
</style>
