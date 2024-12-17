<script setup>
import { formatNumber } from '@/libs/utilities';
import { CButton } from '@coreui/vue';
import { onMounted } from 'vue';
import { castAPI } from '~/libs/api';

const currentPage = ref(1);
const totalPages = ref(0);
const casts = ref([]);

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

onMounted(() => {
  fetchCasts();

});

</script>

<template>

  <div class="container-fluid mt-4">
    <div class="card card-dark">
      <div class="card-body table-card">
        <CButton color="primary" class="fs-3">CSV出力</CButton>
        <div class="row">
          <div class="col-12">
            <table class="table table-hover fs-4">
              <thead>
                <tr>
                  <th scope="col" class="text-center">氏名</th>
                  <th scope="col" class="text-center">ニックネーム</th>
                  <th scope="col" class="text-center">保有ポイント</th>
                  <th scope="col" class="text-center">払い出し可能金額</th>
                  <th scope="col" class="text-center">銀行口座</th>
                </tr>                
              </thead>            
              <tbody>
                <tr v-for="castInfo in casts" :key="castInfo.cast.id">
                  <!-- {{ castInfo }} -->
                  <td>{{ castInfo.cast.name_ja }}</td>
                  <td>{{ castInfo.cast.nickname }}</td>
                  <td>{{ formatNumber(castInfo.cast.points_holded) }}P</td>
                  <td>{{ formatNumber(castInfo.cast.points_holded * castInfo.cast.point_rate/100) }}円</td>
                  <td>
                    銀行名：{{ castInfo.bank_details.bank_name }}<br>
                    支店：{{ castInfo.bank_details.branch }}<br>
                    口座番号：{{ castInfo.bank_details.account_number }}({{ castInfo.bank_details.account_type }})<br>                    
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>
.card-dark .card-header {
  background: #2f353a 0% 0% no-repeat padding-box;
}

.card-dark .card-header h4 {
  color: #ffffff;
}
label {
  font-size: 1.25rem;
}
.form-control,
.form-select {
  height: 43px;  
}
.form-select {
	font-size: 1.6rem;
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
    padding: 9px 40px;
    margin-top: 15px;
    max-width: 328px;
    margin-left: 1em;
    white-space: nowrap;
    color: #FFF;
    margin-right: 12px;
}
.pagination {
	a {
		font-size: 1.5rem !important;
		padding: 3px 10px;
	}
}
</style>