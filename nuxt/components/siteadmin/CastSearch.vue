<script setup>
import { formatNumber, getAlcoholList, getCupSizeList, getFootworkList, getPrefectureList, getRankList, getRankPointValue, getStatus } from '@/libs/utilities';
import { CBadge, CModal, CModalBody, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { authAPI, castAPI, categoryAPI, pointAPI, statusAPI } from '~/libs/api';

const introducerOptions = ref([
  {name: '紹介者1', id: '1'},
  {name: '紹介者2', id: '2'},
  {name: '紹介者3', id: '3'},
]);
const person_in_charge_options = ref([
  {name: '担当1', id: '1'},
  {name: '担当2', id: '2'},
  {name: '担当3', id: '3'},
]);
const ceo_check_options = ref([
  {label: '確認済み', value: true},
  {label: '未確認', value: false},
]);

const current_staff_type = ref('');

const isPointModalVisible = ref(false); // Pointモーダルの表示状態
const name_ja = ref('');
const name_kana = ref('');
const nickname = ref('');
const address = ref('');
const introducer = ref('');
const person_in_charge_id = ref('');
const registered_date = ref('');
const rank = ref('');
const rankOptions = ref(getRankList());
const footwork = ref('');
const footworkOptions = ref(getFootworkList());
const alcohol = ref('');
const alcoholOptions = ref(getAlcoholList());
const nightJobFlag = ref(false);
const category = ref('');
const categoryOptions = ref([]);
const statuses = ref([]);
const status = ref('');
const workStatus = ref('');
const liveStatus = ref('');
const ceo_check = ref('');
const featureTags = ref([]);
const otherFeatures = ref('');
const edit_otherFeatures = ref('');
const casts = ref([]);
const currentPage = ref(1);
const totalPages = ref(0);
const isEditModalVisible = ref(false);
const cup_size = ref('');
const phoneNumber = ref('');
const instagramId = ref('');
const lineId = ref('');
const height = ref('');
const min_height = ref(145);
const max_height = ref(180);
const three_size_b = ref('');
const three_size_w = ref('');
const three_size_h = ref('');
const basic_point_price = ref('');

// const for edit model
const edit_name_ja = ref('');
const edit_name_kana = ref('');
const edit_nickname = ref('');
const edit_email = ref('');
const edit_city = ref('');
const edit_person_in_charge_id = ref('');
const edit_registered_date = ref('');
const edit_status = ref('');
const edit_rank = ref('');
const edit_post_code = ref('');
const edit_prefecture = ref('');
const edit_municipalitie = ref('');
const edit_street = ref('');
const edit_station = ref('');
const edit_footwork = ref('');
const edit_alcohol = ref('');
const edit_day_work = ref('');
const edit_night_work = ref('');
const edit_height = ref('1');
const edit_three_size_b = ref('');
const edit_three_size_w = ref('');
const edit_three_size_h = ref('');
const edit_cup_size = ref('');
const edit_vip_status = ref('');
const edit_birthday = ref(''); // Special handling for this field
const edit_ceo_check = ref('');
const edit_instagram_id = ref('');
const edit_line_id = ref('');
const edit_phone_number = ref('');
const edit_introducer_id = ref('');
const edit_work_status = ref('');
const edit_live_status = ref('');
const edit_point_rate = ref('');
const edit_basic_point_price = ref('');
const edit_feature_tags = ref([]);
const edit_management_tags = ref([]);

const editing_cast_images = ref([]);


// 特徴タグの仮APIデータ
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


const point = ref(0);
const categoryName = ref('');
const managerOptions = ref([
  {name: '担当1', id: '1'},
  {name: '担当2', id: '2'},
  {name: '担当3', id: '3'},
]);
const referralSource = ref('');
const referralSourceOptions = ref([
  {name: '紹介者1', id: '1'},
  {name: '紹介者2', id: '2'},
  {name: '紹介者3', id: '3'},
]);

const prefectureOptions = ref(getPrefectureList());




const editCastId = ref();
const vipServiceOptions = ref([
  {label: '推奨', value: 'good'},
  {label: '可', value: 'average'},
  {label: '不可', value: 'bad'},
  {label: 'Not', value: 'not'},
]);
const originalData = ref({});

const presidentConfirmationOptions = ref([
  {label: 'True', value: true},
  {label: 'False', value: false},
]);

const getFootworkSymbol = (footwork) => {
  switch (footwork) {
    case 'good':
      return '〇';
    case 'average':
      return '△';
    case 'bad':
      return '×';
    default:
      return '−';
  }
};

// 仮 テーブルでのアルコール表示... 強い(good)は'〇'、普通(average)は'△'、弱い(bad)は'×'、NG(not)は'-' 、としています
const getAlcoholSymbol = (alcohol) => {
  switch (alcohol) {
    case 'good':
      return '〇';
    case 'average':
      return '△';
    case 'bad':
      return '×';
    case 'not':
      return '−';
    default:
      return '−';
  }
};

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

const selectedCast = ref({});
const showPointModal = (cast) => {
  selectedCast.value = cast; // 選択された女性会員の情報をセット
  isPointModalVisible.value = true; // モーダルを表示
};

try {
    const response = await authAPI.profile();
    current_staff_type.value = response.data.user_type;
  } catch (error) {
    console.error('Error fetching current user data:', error);
    Swal.fire('Error!', 'Failed to load current user.', 'error');
  }


const editCast = async (id) => {
  console.log(`Editing cast with id: ${id}`);

  try {
    const response = await castAPI.getCastInfo(id);
    const data = response.data.cast;
    editing_cast_images.value = response.data.images;
    console.log(data);
    const fetchCastCategoryName = async (categoryId) => {
      const categoryResponse = await categoryAPI.getCastCategoryName(categoryId);
      category.value = categoryResponse.data.category_name;
      console.log(category.value);
    };

    // Populate form fields with fetched data
    editCastId.value = data.id;
    edit_name_ja.value = data.name_ja;
    edit_name_kana.value = data.name_kana;
    edit_nickname.value = data.nickname;
    edit_email.value = data.email;
    edit_city.value = data.city;
    edit_person_in_charge_id.value = data.person_in_charge_id;
    edit_registered_date.value = data.registered_date;
    edit_status.value = data.status;
    edit_rank.value = data.rank;
    edit_post_code.value = data.post_code;
    edit_prefecture.value = data.prefecture;
    edit_municipalitie.value = data.municipalitie;
    edit_street.value = data.street;
    edit_station.value = data.station;
    edit_footwork.value = data.footwork;
    edit_alcohol.value = data.alcohol;
    edit_day_work.value = data.day_work;
    edit_night_work.value = data.night_work;
    edit_height.value = data.height;
    edit_three_size_b.value = data.three_size_b;
    edit_three_size_w.value = data.three_size_w;
    edit_three_size_h.value = data.three_size_h;
    edit_cup_size.value = data.cup_size;
    edit_vip_status.value = data.vip_status;
    edit_birthday.value = data.birthday ? data.birthday.split(" ")[0] : "";
    edit_ceo_check.value = data.ceo_check?true:false;
    edit_instagram_id.value = data.instagram_id;
    edit_line_id.value = data.line_id;
    edit_point_rate.value = data.point_rate;
    edit_work_status.value = data.work_status;
    edit_live_status.value = data.live_status;
    edit_introducer_id.value = data.introducer_id;
    nightJobFlag.value = data.nightJobFlag;
    categoryName.value = category.value;
    edit_phone_number.value = data.phone_number;
    edit_feature_tags.value = data.tag_of_spec || [];
    edit_management_tags.value = data.management_tags || [];
    edit_otherFeatures.value = data.notices;
    edit_basic_point_price.value = data.basic_point_price;
   
    await fetchCastCategoryName(data.category_id);
    isEditModalVisible.value = true;
  } catch (error) {
    console.error('Error fetching cast data:', error);
    Swal.fire('Error!', 'Failed to load cast details for editing.', 'error');
  }
  // 編集ロジックをここに実装
};

const savePoint = async (data,pointValue) => {
  // Define a mapping of form fields to original data keys
  const sendData = {
    user_id: data.users_table_id, // bigint(20) unsigned
    point_amount: pointValue, // int(11)
  };

  try {
    await pointAPI.createWithHoldedPoint(sendData);
    isPointModalVisible.value = false;
    Swal.fire('Success!', 'ポイント処理が成功しました', 'success');
    handleSubmit();

  } catch (error) {
    console.error('Error updating cast data:', error);
    Swal.fire('Error!', 'ポイント処理が失敗しました', 'error');
  }
};

const saveCastChanges = async (id) => {
  console.log("castId", id);
  const updatedFields = {};

  // Define a mapping of form fields to original data keys
  const fieldMapping = {
    edit_name_ja: 'name_ja',
    edit_name_kana: 'name_kana',
    edit_nickname: 'nickname',
    edit_email: 'email',
    edit_city: 'city',
    edit_person_in_charge_id: 'person_in_charge_id',
    edit_registered_date: 'registered_date',
    edit_status: 'status',
    edit_work_status: 'work_status',
    edit_live_status: 'live_status',
    edit_rank: 'rank',
    edit_post_code: 'post_code',
    edit_prefecture: 'prefecture',
    edit_municipalitie: 'municipalitie',
    edit_street: 'street',
    edit_station: 'station',
    edit_footwork: 'footwork',
    edit_alcohol: 'alcohol',
    edit_day_work: 'day_work',
    edit_night_work: 'night_work',
    edit_height: 'height',
    edit_three_size_b: 'three_size_b',
    edit_three_size_w: 'three_size_w',
    edit_three_size_h: 'three_size_h',
    edit_cup_size: 'cup_size',
    edit_vip_status: 'vip_status',
    edit_birthday: 'birthday', 
    edit_ceo_check: 'ceo_check',
    edit_instagram_id: 'instagram_id',
    edit_line_id: 'line_id',
    edit_phone_number: 'phone_number',
    edit_introducer_id: 'introducer_id',    
    edit_point_rate: 'point_rate',
    edit_basic_point_price:'basic_point_price',
    edit_feature_tags: 'tag_of_spec',
    edit_management_tags: 'management_tags',
  };

  Object.entries(fieldMapping).forEach(([fieldRef, originalKey]) => {
    const currentValue = eval(fieldRef).value; // Using eval to access ref values
    const originalValue = originalData.value[originalKey];
    console.log(fieldRef);
    console.log(originalKey);
    console.log(currentValue);
    console.log(originalValue);
    if (currentValue !== originalValue) {
      updatedFields[originalKey] = currentValue;
    }
  });

  try {
    await castAPI.castUpdate(id, updatedFields);
    isEditModalVisible.value = false;
    Swal.fire('Success!', 'Cast details updated successfully!', 'success');
    handleSubmit();

  } catch (error) {
    console.error('Error updating cast data:', error);
    Swal.fire('Error!', 'Failed to update cast details.', 'error');
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('ja-JP', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  }).format(date);
};
const handleSubmit = () => {
  const params = {
    name_ja: name_ja.value,
    name_kana: name_kana.value,
    nickname: nickname.value,
    address: address.value,
    introducer: introducer.value,
    person_in_charge: person_in_charge_id.value,
    registered_date: registered_date.value,
    rank: rank.value,
    footwork: footwork.value,
    alcohol: alcohol.value,
    nightJobFlag: nightJobFlag.value,
    height: min_height.value+"-"+max_height.value,
    min_height: min_height.value,
    max_height: max_height.value,
    cup_size: cup_size.value,
    three_size_b: three_size_b.value,
    three_size_w: three_size_w.value,
    three_size_h: three_size_h.value,
    category: category.value,
    status: status.value,
    work_status: workStatus.value,
    live_status: liveStatus.value,
    ceo_check: ceo_check.value,
    instagramId: instagramId.value,
    line_id: lineId.value,
    phone_number: phoneNumber.value,
    featureTags: featureTags.value,
    otherFeatures: otherFeatures.value,
    basic_point_price: basic_point_price.value,
    // Add other form fields here
  };
  // console.log('handleSubmit', params);
  fetchCasts(params);
};
const fetchCasts = async (params = {}) => {
  try {
    params.page = currentPage.value; // Add page parameter
    console.log(params);
    const response = await castAPI.getAll(params);
    casts.value = response.data.casts;
    totalPages.value = response.data.pages; // Set total pages
  } catch (error) {
    console.log('Error fetching casts:', error);
  }
};
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchCasts();
  }
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

const deleteCast = async (id) => {
  try {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
      await castAPI.deleteCast(id);
      Swal.fire('Deleted!', 'The cast has been deleted.', 'success');
      fetchCasts(); // Refresh the list after deletion
    }
  } catch (error) {
    Swal.fire('Error!', 'Failed to delete the cast.', 'error');
    console.error('Error deleting cast:', error);
  }
};

const reloadPage = () => {
    name_ja.value = "";
    name_kana.value = "";
    nickname.value = "";
    address.value = "";
    introducer.value = "";
    person_in_charge_id.value = "";
    registered_date.value = "";
    rank.value = "";
    footwork.value = "";
    alcohol.value = "";
    nightJobFlag.value = "";
    height.value = "";
    min_height.value = 145;
    max_height.value = 180;
    cup_size.value = "";
    three_size_b.value = "";
    three_size_w.value = "";
    three_size_h.value = "";
    category.value = "";
    status.value = "";
    workStatus.value = "";
    liveStatus.value = "";
    ceo_check.value = "";
    instagramId.value = "";
    lineId.value = "";
    phoneNumber.value = "";
    featureTags.value = "";
    otherFeatures.value = "";
    basic_point_price.value = "";
    handleSubmit();
};

const fetchStatuses = async () => {
  try {
    const response = await statusAPI.getAll();
    console.log('response', response);
    statuses.value = response.data;
  } catch (error) {
    console.log('Error fetching categories:', error);
  }
};



onMounted(() => {
  console.log('onMounted');
  fetchCasts();
  fetchCategories();
  fetchStatuses();
      
  const intervalId = setInterval(() => {
    fetchCasts();
  }, 60000); // 60000ミリ秒 = 1分

  // コンポーネントがアンマウントされたときにインターバルをクリア
  onUnmounted(() => {
    clearInterval(intervalId);
  });

});
</script>

<template>
  <div>
    <div>
      <div class="d-flex justify-content-between align-items-center content-header">
        <h4 class="fw-bold fs-3">女性会員検索</h4>
      </div>
      <!-- search menu section -->
      <div class="container">
        <div class="card card-dark mt-4">
          <div class="card-header">
            <h4 class="fs-3 fw-normal">検索メニュー</h4>
          </div>
          <div class="card-body">
            <div class="register-area">
              <form @submit.prevent="handleSubmit">
                <div class="d-flex search-area">
                  <div class="main-area">
                    <div class="d-flex justify-content-between align-items-end search-input fs-5">
                      <div>
                        <label for="name_ja">名前</label>
                        <input type="text" v-model="name_ja" id="name_ja" class="form-control" placeholder="名前を入力" />
                      </div>
                      <div>
                        <label for="name_kana">ふりがな</label>
                        <input type="text" v-model="name_kana" id="name_kana" class="form-control"
                          placeholder="ふりがなを入力" />
                      </div>
                      <div>
                        <label for="nickname">ニックネーム</label>
                        <input type="text" v-model="nickname" id="nickname" class="form-control"
                          placeholder="ニックネームを入力" />
                      </div>
                      <!-- <div> -->
                      <!-- <label for="address">住所</label> -->
                      <!-- <input type="text" v-model="address" id="address" class="form-control" -->
                      <!-- placeholder="大まかな住所を入力"/> -->
                      <!-- </div> -->
                      <div class="select-wrapper">
                        <label for="introducer">紹介元</label>
                        <select v-model="introducer" id="introducer" class="form-select introducer-input fs-4">
                          <option disabled value="">紹介者選択</option>
                          <option v-for="option in introducerOptions" :key="option.name" :value="option.id">
                            {{ option.name }}
                          </option>
                        </select>
                      </div>
                      <div class="select-wrapper">
                        <label for="person_in_charge_id">担当</label>
                        <select v-model="person_in_charge_id" id="person_in_charge_id"
                          class="form-select person-in-charge-input fs-4">
                          <option disabled value="">担当選択</option>
                          <option v-for="option in person_in_charge_options" :key="option.name" :value="option.id">
                            {{ option.name }}
                          </option>
                        </select>
                      </div>
                    </div>
                    <div class="d-flex search-input fs-5">
                      <div>
                        <label for="registered_date">登録日</label>
                        <input type="date" v-model="registered_date" id="registered_date" class="form-control" />
                      </div>
                      <div>
                        <label for="rank">クラス</label>
                        <select v-model="rank" name="" id="rank" class="form-select rank-input fs-4">
                          <option disabled value="">クラス選択</option>
                          <option v-for="option in rankOptions" :key="option.label" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </div>
                      <div>
                        <label for="point">Deet報酬ポイント</label>
                        <input type="number" v-model="basic_point_price" id="point" class="form-control" />
                      </div>
                      <div>
                        <label for="footwork">フットワーク</label>
                        <select v-model="footwork" name="" id="footwork" class="form-select footwork-input fs-4">
                          <option disabled value="">選択</option>
                          <option v-for="option in footworkOptions" :key="option.label" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </div>
                      <div>
                        <label for="alcohol">酒</label>
                        <select v-model="alcohol" name="" id="alcohol" class="form-select alcohol-input fs-4">
                          <option disabled value="">選択</option>
                          <option v-for="option in alcoholOptions" :key="option.label" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </div>
                      <div class="night-job-flag-container">
                        <div
                          class="form-check form-switch d-flex flex-column justify-content-center align-items-center night-job-flag mb-1">
                          <label class="form-check-label mb-1" for="flexSwitchCheckDefault">夜職フラグ</label>
                          <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                            v-model="nightJobFlag" />
                        </div>
                      </div>
                    </div>

                    <div class="d-flex search-input fs-5">
                      <div>
                        <label for="min_height">身長(min)</label>
                        <input type="number" v-model="min_height" id="min_height" class="form-control" placeholder="身長を入力" />
                      </div>
                      <div>
                        <label for="max_height">身長(max)</label>
                        <input type="number" v-model="max_height" id="max_height" class="form-control" placeholder="身長を入力" />
                      </div>
                      <div>
                        <label for="bust" class="">スリーサイズ</label>
                        <div class="d-flex align-items-center">
                          <input type="number" v-model="three_size_b" placeholder="B" id="bust" class="form-control" />
                          <span class="mx-2"> - </span>
                          <input type="number" v-model="three_size_w" placeholder="W" class="form-control" />
                          <span class="mx-2"> - </span>
                          <input type="number" v-model="three_size_h" placeholder="H" class="form-control" />
                        </div>
                      </div>
                      <div>
                        <label for="cup_size">カップ数</label>
                        <select v-model="cup_size" name="" id="cup_size" class="form-select fs-5">
                          <option disabled value="">選択</option>
                          <option v-for="option in getCupSizeList()" :key="option.label" :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </div>
                    </div>
                    <div class="d-flex search-input fs-5">
                      <div>
                        <label for="lineId">LineID</label>
                        <input type="text" v-model="lineId" id="lineId" class="form-control" placeholder="LINEのIDを入力" />
                      </div>
                      <div>
                        <label for="instagramId">インスタID</label>
                        <input type="text" v-model="instagramId" id="instagramId" class="form-control"
                          placeholder="インスタのID" />
                      </div>
                      <div>
                        <label for="phoneNumber">TEL</label>
                        <input type="tel" v-model="phoneNumber" id="phoneNumber" class="form-control"
                          placeholder="Default input" />
                      </div>
                      <div>
                        <label for="category">カテゴリ</label>
                        <select v-model="category" name="" id="category"
                          class="form-select register-input category-input fs-4">
                          <option disabled value="">カテゴリ選択</option>
                          <option v-for="option in categoryOptions" :key="option.category_name" :value="option.id">
                            {{ option.category_name }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="status-area">
                    <div class="search-input">
                      <label for="status">会員ステータス</label>
                      <select v-model="status" name="" id="status" class="form-select register-input fs-4">
                        <option disabled value="">稼働状況を選択</option>
                        <option v-for="option in statuses.cast" :key="option.id" :value="option.id">
                          {{ option.status_name }}
                        </option>
                      </select>
                    </div>
                    <div class="search-input">
                      <label for="workStatus">業務ステータス</label>
                      <select v-model="workStatus" name="" id="workStatus" class="form-select register-input fs-4">
                        <option disabled value="">稼働状況を選択</option>
                        <option v-for="option in statuses.cast_work" :key="option.id" :value="option.id">
                          {{ option.status_name }}
                        </option>
                      </select>
                    </div>
                    <div class="search-input">
                      <label for="liveStatus">ライブステータス</label>
                      <select v-model="liveStatus" name="" id="liveStatus" class="form-select register-input fs-4">
                        <option disabled value="">稼働状況を選択</option>
                        <option v-for="option in statuses.cast_live" :key="option.id" :value="option.id">
                          {{ option.status_name }}
                        </option>
                      </select>
                    </div>
                    <div class="search-input">
                      <label for="ceo_check">社長確認</label>
                      <select v-model="ceo_check" name="" id="ceo_check" class="form-select register-input fs-4">
                        <option disabled value="">選択</option>
                        <option v-for="option in ceo_check_options" :key="option.label" :value="option.value">
                          {{ option.label }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                <SiteadminTagSelector v-model:selected-tags="featureTags" :all-tags="allFeatureTags"
                  label="特徴タグ (AND検索)" class="fs-5" />
                <div class="search-input other-features-input fs-4">
                  <label for="otherFeatures">その他特徴</label>
                  <input type="text" v-model="otherFeatures" name="" id="otherFeatures" class="form-control" />
                </div>
                <div class="button-container">
                  <button type="submit" class="reset-btn btn btn-secondary" @click="reloadPage">
                    条件リセット
                  </button>
                  <button type="submit" class="btn deet-btn">
                    検索
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- search results section -->
      <div class="container mt-4">
        <div class="card card-dark">
          <div class="card-header">
            <h4 class="fs-3 fw-normal" style="font-size:20px;">検索結果</h4>
          </div>
          <div class="card-body table-card">
            <table class="table table-hover fs-4">
              <thead>
                <tr>
                  <th scope="col" class="text-center">ID</th>
                  <th scope="col" class="text-center">ステータス</th>
                  <th scope="col" class="text-center">稼働ステータス</th>
                  <th scope="col" class="text-center">Liveステータス</th>
                  <th scope="col" class="text-center">クラス/30min</th>
                  <th scope="col" class="text-center">Deet/30min</th>
                  <th scope="col" class="text-center" style="width:15em;">女性会員名/ニックネーム</th>
                  <th scope="col" class="text-center">フットワーク</th>
                  <th scope="col" class="text-center">酒</th>
                  <th scope="col" class="text-center" style="width:5em;">保有ポイント</th>
                  <!-- <th scope="col" class="text-center">更新日</th> -->
                  <!-- <th scope="col" class="text-center">登録日</th> -->
                  <th scope="col" class="text-center button-row" style="width:5em;">編集</th>
                  <th scope="col" class="text-center button-row" style="width:5em;">Point調整</th>
                  <!-- <th v-if="current_staff_type==='admin'" scope="col" class="text-center button-row">アカウント削除</th> -->
                </tr>
              </thead>
              <tbody>
                <tr v-for="castInfo in casts" :key="castInfo.id">
                  <td class="text-center">{{ castInfo.cast.id }}</td>
                  <td class="text-center">
                    <CBadge class="fs-5" :class="'status-'+castInfo.cast.status">{{ getStatus(castInfo.cast.status) }}</CBadge>
                    <!-- <span class="fs-5 me-2 status-label" :class="`label-${castInfo.cast.status}`">{{ -->
                      <!-- getStatus(castInfo.cast.status) }}</span> -->
                  </td>
                  <td class="text-center">
                    <CBadge class="fs-5" :class="'status-'+castInfo.cast.work_status">{{ getStatus(castInfo.cast.work_status) }}</CBadge>
                    <!-- <span class="fs-5 me-2 status-label" :class="`label-${castInfo.cast.work_status}`">{{ -->
                      <!-- getStatus(castInfo.cast.work_status) }}</span> -->
                  </td>
                  <td class="text-center">
                    <CBadge class="fs-5" :class="'status-'+castInfo.cast.live_status">{{ getStatus(castInfo.cast.live_status) }}</CBadge>
                    <!-- <span class="fs-5 me-2 status-label" :class="`label-${castInfo.cast.live_status}`">{{ -->
                      <!-- getStatus(castInfo.cast.live_status) }}</span> -->
                  </td>
                  <td class="text-center">{{ castInfo.cast.rank }} / {{
                    formatNumber(getRankPointValue(castInfo.cast.rank))}}</td>
                  <td class="text-center">{{ formatNumber(castInfo.cast.basic_point_price) }}</td>
                  <td class="text-center">
                    {{ castInfo.cast.name_ja }} / {{ castInfo.cast.nickname }}
                  </td>
                  <td class="text-center fw-normal">
                    {{ getFootworkSymbol(castInfo.cast.footwork) }}
                  </td>
                  <td class="text-center fw-normal">
                    {{ getAlcoholSymbol(castInfo.cast.alcohol) }}
                  </td>
                  <td class="text-center fw-normal">
                    {{ formatNumber(castInfo.cast.points_holded) }}
                  </td>
                  <!-- <td class="text-center fw-normal"> -->
                  <!-- {{ formatDate(castInfo.cast.updated_at) }} -->
                  <!-- </td> -->
                  <!-- <td class="text-center fw-normal"> -->
                  <!-- {{ formatDate(castInfo.cast.registered_date) }} -->
                  <!-- </td> -->
                  <td class="text-center">
                    <button v-if="current_staff_type==='admin'" class="btn btn-deet-transparent fs-5"
                      @click="editCast(castInfo.cast.id)">
                      編集
                    </button>
                    <button v-else class="btn btn-deet-transparent fs-5" @click="editCast(castInfo.cast.id)">
                      詳細
                    </button>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-deet-transparent fs-5" @click="showPointModal(castInfo.cast)">
                      調整
                    </button>
                  </td>
                  <!-- <td v-if="current_staff_type==='admin'" class="text-center"> -->
                    <!-- <button class="table-btn btn btn-danger fs-5" @click="deleteCast(castInfo.cast.id)"> -->
                      <!-- 削除 -->
                    <!-- </button> -->
                  <!-- </td> -->
                </tr>
              </tbody>
            </table>
          </div>
          <!-- ページネーションのサンプル置いただけ、後から実装する -->
          <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center pagination-sm custom-pagination">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click="goToPage(currentPage - 1)">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
              <li class="page-item" v-for="page in Array.from({ length: totalPages }, (_, i) => i + 1)" :key="page"
                :class="{ active: currentPage === page }">
                <a class="page-link" href="#" @click="goToPage(page)">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click="goToPage(currentPage + 1)">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>



    <div v-if="isEditModalVisible" class="modal-overlay">
      <!-- Modal Content -->
      <div class="modal-content">

        <form @submit.prevent="handleSubmit">

          <div class="container">
            <div class="card">
              <div class="card-header">
                <h4>基本情報</h4>
                <button @click="isEditModalVisible = false" class="close-btn">&times;</button>
              </div>

              <div class="card-body">
                <div class="register-area">
                  <!-- Upper part from hr -->
                  <div class="cast-images" v-if="editing_cast_images.length > 0">
                    <h2>女性会員画像</h2>
                    <div class="container-fluid">
                    <div class="row">                      
                      <div class="col-2" v-for="image in editing_cast_images" :key="image.id">
                        <img class="cast-image" :src="image.image_url" :alt="image.title || '女性会員画像'" />
                      </div>
                    </div>
                    </div>
                  </div>

                  <div class="upper-area">
                    <div class="main-area">
                      <!-- 1st row -->
                      <div class="d-flex justify-content-between align-items-end register-input">
                        <div>
                          <label for="editNameJa">名前</label>
                          <input type="text" v-model="edit_name_ja" id="editNameJa" class="form-control"
                            placeholder="名前を入力" />
                        </div>
                        <div>
                          <label for="edit_name_kana">ふりがな</label>
                          <input type="text" v-model="edit_name_kana" id="edit_name_kana" class="form-control"
                            placeholder="ふりがなを入力" />
                        </div>
                        <div>
                          <label for="edit_nickname">ニックネーム</label>
                          <input type="text" v-model="edit_nickname" id="edit_nickname" class="form-control"
                            placeholder="ニックネームを入力" />
                        </div>
                        <div>
                          <label for="edit_station">最寄り駅</label>
                          <input type="text" v-model="edit_station" id="edit_station" class="form-control"
                            placeholder="駅名を入力" />
                        </div>
                        <div class="select-wrapper">
                          <label for="edit_introducer_id">紹介元</label>
                          <select v-model="edit_introducer_id" id="edit_introducer_id"
                            class="form-select manager-input fs-5">
                            <option disabled value="">紹介元選択</option>
                            <option v-for="option in referralSourceOptions" :key="option.name" :value="option.id">
                              {{ option.name }}
                            </option>
                          </select>
                          <!-- 
                          <multiselect v-model="edit_introducer_id" :options="referralSourceOptions" id="edit_introducer_id" label="name"
                            :searchable="true" placeholder="紹介者選択" track-by="id" style="min-width: 230px" /> -->
                        </div>
                        <div class="select-wrapper">
                          <label for="edit_person_in_charge_id">担当</label>
                          <select v-model="edit_person_in_charge_id" id="edit_person_in_charge_id"
                            class="form-select manager-input fs-5">
                            <option disabled value="">担当選択</option>
                            <option v-for="option in managerOptions" :key="option.name" :value="option.id">
                              {{ option.name }}
                            </option>
                          </select>
                        </div>
                        <div>
                          <label for="edit_registered_date">登録日</label>
                          <input type="date" v-model="edit_registered_date" id="edit_registered_date"
                            class="form-control" />
                        </div>
                      </div>

                      <!-- 2nd row -->
                      <div class="d-flex justify-content-between align-items-end register-input second-row">
                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_post_code">郵便番号</label>
                          <input type="text" v-model="edit_post_code" id="edit_post_code" class="form-control"
                            placeholder="1234567" @blur="fetchAddress" />
                        </div>
                        <div>
                          <label for="edit_prefecture">都道府県</label>
                          <select v-model="edit_prefecture" id="edit_prefecture"
                            class="form-select prefecture-input fs-5">
                            <option disabled value="">選択</option>
                            <option v-for="option in prefectureOptions" :key="option" :value="option">
                              {{ option }}
                            </option>
                          </select>
                        </div>
                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_municipalitie">市区町村</label>
                          <input type="text" v-model="edit_municipalitie" id="edit_municipalitie" class="form-control"
                            placeholder="Default input" />
                        </div>
                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_street">町名など</label>
                          <input type="text" v-model="edit_street" id="edit_street" class="form-control"
                            placeholder="Default input" />
                        </div>
                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_building">番地以降</label>
                          <input type="text" v-model="edit_building" id="edit_building" class="form-control"
                            placeholder="Default input" />
                        </div>
                      </div>

                      <!-- 3rd row -->
                      <div class="d-flex justify-content-between align-items-end register-input">
                        <div>
                          <label for="edit_phone_number">TEL</label>
                          <input type="tel" v-model="edit_phone_number" id="edit_phone_number" class="form-control"
                            placeholder="TELを入力" />
                        </div>
                        <div>
                          <label for="edit_height">身長</label>
                          <input type="number" v-model="edit_height" id="edit_height" class="form-control"
                            placeholder="身長を入力" />
                        </div>
                        <div>
                          <label for="bust" class="">スリーサイズ</label>
                          <div class="d-flex align-items-center">
                            <input type="number" v-model="edit_three_size_b" placeholder="B" id="bust"
                              class="form-control" />
                            <span class="mx-2"> - </span>
                            <input type="number" v-model="edit_three_size_w" placeholder="W" class="form-control" />
                            <span class="mx-2"> - </span>
                            <input type="number" v-model="edit_three_size_h" placeholder="H" class="form-control" />
                          </div>
                        </div>
                        <div>
                          <label for="edit_cup_size">カップ数</label>
                          <select v-model="edit_cup_size" name="" id="edit_cup_size" class="form-select fs-5">
                            <option disabled value="">選択</option>
                            <option v-for="option in getCupSizeList()" :key="option.label" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>

                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_final_academic_background">最終学歴</label>
                          <input type="text" v-model="edit_final_academic_background"
                            id="edit_final_academic_background" class="form-control" placeholder="最終学歴を入力" />
                        </div>
                      </div>

                      <!-- 4th row -->
                      <div class="d-flex justify-content-between align-items-end register-input">
                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_birthday">生年月日</label>
                          <input type="date" v-model="edit_birthday" id="edit_birthday" class="form-control" />
                        </div>

                        <div>
                          <label for="edit_rank">クラス</label>
                          <select v-model="edit_rank" name="" id="edit_rank" class="form-select rank-input fs-5">
                            <option disabled value="">クラス選択</option>
                            <option v-for="option in rankOptions" :key="option.label" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        <div>
                          <label for="edit_footwork">フットワーク</label>
                          <select v-model="edit_footwork" name="" id="edit_footwork"
                            class="form-select footwork-input fs-5">
                            <option disabled value="">選択</option>
                            <option v-for="option in footworkOptions" :key="option.label" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        <div>
                          <label for="edit_alcohol">酒</label>
                          <select v-model="edit_alcohol" name="" id="edit_alcohol"
                            class="form-select alcoholTolerance-input fs-5">
                            <option disabled value="">選択</option>
                            <option v-for="option in alcoholOptions" :key="option.label" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        <div>
                          <label for="edit_day_work">昼職</label>
                          <input type="text" v-model="edit_day_work" id="edit_day_work" class="form-control"
                            placeholder="職業を入力" />
                        </div>
                        <div>
                          <label for="edit_night_work">夜職</label>
                          <input type="text" v-model="edit_night_work" id="edit_night_work" class="form-control"
                            placeholder="職業を入力" />
                        </div>


                      </div>

                      <!-- 5th row -->
                      <div class="d-flex justify-content-between align-items-end register-input">
                        <div>
                          <label for="edit_vip_status">VIP対応</label>
                          <select v-model="edit_vip_status" name="" id="edit_vip_status"
                            class="form-select vip-input fs-5">
                            <option disabled value="">選択</option>
                            <option v-for="option in vipServiceOptions" :key="option.label" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>

                        <div>
                          <label for="edit_basic_point_price">Deet報酬ポイント</label>
                          <input type="number" v-model="edit_basic_point_price" id="edit_basic_point_price"
                            class="form-control" placeholder="ポイント換金レート" />
                        </div>
                        <div>
                          <label for="edit_point_rate">ポイント換金レート</label>
                          <input type="number" v-model="edit_point_rate" id="edit_point_rate" class="form-control"
                            placeholder="ポイント換金レート" min="70" max="100" />
                        </div>

                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_email">メールアドレス</label>
                          <input type="text" v-model="edit_email" id="edit_email" class="form-control"
                            placeholder="メールアドレスを入力" />
                        </div>

                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_line_id">LineID</label>
                          <input type="text" v-model="edit_line_id" id="edit_line_id" class="form-control"
                            placeholder="LINEのIDを入力" />
                        </div>
                        <div v-if="current_staff_type==='admin'">
                          <label for="edit_instagram_id">インスタID</label>
                          <input type="text" v-model="edit_instagram_id" id="edit_instagram_id" class="form-control"
                            placeholder="インスタのIDを入力" />
                        </div>
                      </div>
                    </div>

                    <!-- side area -->
                    <div class="status-area">
                      <div>
                        <label for="edit_status">会員ステータス</label>
                        <select v-model="edit_status" name="" id="edit_status" class="form-select register-input fs-5">
                          <option disabled value="">ステータスを選択</option>
                          <option v-for="option in statuses.cast" :key="option.id" :value="option.id">
                            {{ option.status_name }}
                          </option>
                        </select>
                      </div>
                      <div>
                        <label for="edit_work_status">業務ステータス</label>
                        <select v-model="edit_work_status" name="" id="edit_work_status"
                          class="form-select register-input fs-5">
                          <option disabled value="">ステータスを選択</option>
                          <option v-for="option in statuses.cast_work" :key="option.id" :value="option.id">
                            {{ option.status_name }}
                          </option>
                        </select>
                      </div>
                      <div>
                        <label for="edit_live_status">ライブステータス</label>
                        <select v-model="edit_live_status" name="" id="edit_live_status"
                          class="form-select register-input fs-5">
                          <option disabled value="">ステータスを選択</option>
                          <option v-for="option in statuses.cast_live" :key="option.id" :value="option.id">
                            {{ option.status_name }}
                          </option>
                        </select>
                      </div>
                      <div>
                        <label for="edit_ceo_check">社長確認</label>
                        <select v-model="edit_ceo_check" name="" id="edit_ceo_check"
                          class="form-select register-input fs-5">
                          <option disabled value="">選択</option>
                          <option v-for="option in presidentConfirmationOptions" :key="option.label"
                            :value="option.value">
                            {{ option.label }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <hr />

                  <!-- Lower part from hr -->
                  <div class="mb-4">
                    <label for="category">カテゴリ</label>
                    <select v-model="category" id="category" class="form-select fs-4">
                      <option :value="category">{{ category }}</option>
                      <option disabled value="">カテゴリ選択</option>
                      <option v-for="option in categoryOptions" :key="option.id" :value="option.id">
                        {{ option.category_name }}
                      </option>
                    </select>
                  </div>

                  <SiteadminTagSelector v-model:selected-tags="edit_feature_tags" :all-tags="allFeatureTags"
                    label="特徴タグ" />
                  <SiteadminTagSelector v-model:selected-tags="edit_management_tags" :all-tags="allManagementTags"
                    label="管理タグ" />

                  <!-- <SiteadminTagSelector v-model="managementTags" :allTags="allManagementTags" label="管理タグ" /> -->

                  <div>
                    <label for="otherFeatures">その他特徴</label>
                    <textarea v-model="otherFeatures" name="" id="otherFeatures"
                      class="form-control register-input"></textarea>
                  </div>
                  <div v-if="current_staff_type==='admin'" class="d-flex justify-content-end align-items-center">
                    <button class="table-btn btn btn-danger fs-5 mt-3" @click="deleteCast(editCastId)">
                      削除
                    </button>
                    <button @click="saveCastChanges(editCastId)" type="submit" class="btn deet-btn btn-sm">保存</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <CModal title="女性会員ポイント増減" :visible="isPointModalVisible" v-on:close="() => { isPointModalVisible = false; }">
    <CModalHeader>
      <h5 class="modal-title fs-4">Point調整</h5>
    </CModalHeader>
    <CModalBody>
      <p class="fs-5"><strong>ID:</strong> {{ selectedCast.id }}</p>
      <p class="fs-5"><strong>名前:</strong> {{ selectedCast.name_ja }}</p>
      <p class="fs-5"><strong>ニックネーム:</strong> {{ selectedCast.nickname }}</p>
      <p class="fs-5"><strong>クラス:</strong> {{ selectedCast.rank }}</p>

      <label>ポイント増減</label>
      <input class="edit-point-input form-control" type="number" v-model="pointValue" step="1000">

      <div class="text-end">
        <button class="btn deet-btn btn-sm" @click="savePoint(selectedCast, pointValue)">確定</button>
      </div>
    </CModalBody>
  </CModal>



</template>

<style scoped>
.cast-image {
  width: 100%;
}
.modal-overlay {
  .register-area {
    max-height: calc(80dvh - 60px);
    overflow-y: scroll;
  }
}
#editCupSize {
  width: 7rem;
}
.pagination {
	a {
		font-size: 1.5rem !important;
		padding: 3px 10px;
	}
}
.btn-deet-transparent {
  border-color: transparent; 
  color: #FFF;
}
.btn-danger {
  border-color: transparent;  
  background-color: var(--deet-color-status-out-of-front);
  color: #FFF;
  padding: 0.3rem 1.2rem
}
.deet-btn {
  font-weight: normal !important;
  .btn-sm {
    padding: 5px 40px;
  }
}
.reset-btn {
    font-size: 16px;
    line-height: 24px;
    font-weight: normal !important;
    /* color: rgb(29, 26, 22) !important; */
    /* background: rgb(193 160 108); */
    padding: 5px 40px;
    margin-top: 15px;
    max-width: 328px;
    margin-left: 1em;
    white-space: nowrap;
    color: #FFF;
    margin-right: 12px;
}
label {
  font-size: 1.25rem;
}
.form-control,
.form-select {
  height: 43px;  
}
.container {
	max-width: 1570px;
	padding: 0 60px;
}
#flexSwitchCheckDefault {
  font-size: 2.5em;
}
.search-input {
  margin-bottom: 20px;
}

.search-input > div {
  margin-right: 20px;
}

.search-input > div:last-child {
  margin-right: 0;
}

.introducer-input {
  /* min-width: 130px; */
}

.person-in-charge-input {
  /* min-width: 114px; */
}

.rank-input {
  /* min-width: 114px; */
}

.footwork-input {
  /* min-width: 82px; */
}

.alcohol-input {
  /* min-width: 82px; */
}

.category-input {
  /* min-width: 128px; */
}

.button-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
  padding-right: 20px;
}

.reset-btn {
  /* font: normal normal normal 12px/16px Meiryo UI; */
  /* color: #3c4b64; */
  /* margin-right: 12px; */
}

.search-btn {
  /* font: normal normal normal 12px/16px Meiryo UI; */
  /* color: #ffffff; */
}

.search-btn:hover {
  color: #ffffff;
}

.status-area {
  margin-left: 40px;
}

.other-features-input {
  max-width: 1070px;
}

/* 夜職フラグ微調整 */
.night-job-flag-container {
  display: flex;
  align-items: center;
  height: 100%;
  margin-right: 20px;
  .input {
    font-size: 2.5em;
  }
}

.night-job-flag {
  height: 100%;
  min-height: 60px;
  width: 100px;
}

.night-job-flag .form-check-input {
  margin-left: 0;
}

.form-switch {
  padding-left: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.form-check {
  padding-left: 0;
  margin-bottom: 0;
}

/* 以下テーブル幅微調整 */
.table {
  table-layout: fixed;
  width: 100%;
}



.table td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* 以下置いただけページネーションの微調整 */
.custom-pagination .page-link {
  color: #b2b2b2;
  background-color: #fff;
  border-color: #b2b2b2;
  margin-top: 40px;
}

.custom-pagination .page-item.active .page-link {
  color: #fff;
  background-color: #b2b2b2;
  border-color: #b2b2b2;
}

.custom-pagination .page-link:focus,
.custom-pagination .page-link:hover {
  color: #fff;
  background-color: #b2b2b2;
  border-color: #b2b2b2;
}

/* General modal overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  margin-top: 4px;
}

/* Modal content container */

.content-header {
  padding: 11px 24px;
  background: #ffffff 0% 0% no-repeat padding-box;
}

.content-header h4 {
  /* font: normal normal normal 16px/20px Meiryo UI; */
  margin: 0;
  color: #3c4b64;
}

/* Header styling */
.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  border-bottom: 1px solid #ddd;
}

/* Button styling */
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
  /* min-width: 114px; */
}

.footwork-input {
  /* min-width: 82px; */
}

.alcoholTolerance-input {
  /* min-width: 82px; */
}

.vip-input {
  /* min-width: 82px; */
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


.close-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  background: transparent;
  border: none;
  font-size: 24px;
  color: #333;
  cursor: pointer;
  font-weight: bold;
}

.close-btn:hover {
  color: #ff0000;
}

@media (max-width: 768px) {
  .container {
    padding: 20px;
  }

  .search-area {
    flex-direction: column;
  }

  .main-area,
  .status-area {
    width: 100%;
    max-width: 100%;
    margin-left: 0;
  }

  .search-input {
    flex-direction: column;
  }

  .search-input > div {
    width: 100%;
    margin-right: 0;
    margin-bottom: 15px;
  }

  .select-wrapper,
  .form-control,
  .form-select {
    width: 100%;
  }

  .button-container {
    flex-direction: column;
  }

  .button-container button {
    margin-bottom: 20px;
  }

  .reset-btn {
    margin-right: 0;
  }

  .table {
    table-layout: auto;
  }

  .table th,
  .table td {
    width: auto;
  }

  .table-responsive {
    overflow-x: auto;
  }

  .table th,
  .table td {
    white-space: nowrap;
  }
}
</style>
