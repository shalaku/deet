<script setup></script>

<template>
  <main>
    <div class="container mt-5">
      <div class="row">
        <div class="col-12">
          <h2 class="fs-1 mb-4">特定商法に基づく表示</h2>
          <table class="table table-bordered fs-4">
            <tbody>
              <tr>
                <th>会社名</th>
                <td>株式会社VALUE CREATE</td>
              </tr>
              <tr>
                <th>所在地</th>
                <td>東京都港区六本木4-12-12穂高ビル5階</td>
              </tr>
              <tr>
                <th>電話番号</th>
                <td>03-6455-4774</td>
              </tr>
              <tr>
                <th>メールアドレス</th>
                <td>info@deet.jp</td>
              </tr>
              <tr>
                <th>設立年月日</th>
                <td>令和3年10月15日</td>
              </tr>
              <tr>
                <th>代表責任者</th>
                <td>山口 侃</td>
              </tr>
              <tr>
                <th>資本金</th>
                <td>800万円</td>
              </tr>
              <tr>
                <th>追加手数料等</th>
                <td>追加手数料は発生しません</td>
              </tr>
              <tr>
                <th>お支払方法</th>
                <td>クレジットカード決済<br>銀行振込</td>
              </tr>
              <tr>
                <th>代金の支払時期</th>
                <td>毎月1日の月額料金<br>成果が発生した都度発生</td>
              </tr>
              <tr>
                <th>商品のお届け</th>
                <td>代金決済後、すぐにご利用開始いただけます。</td>
              </tr>
              <tr>
                <th>途中退会の場合</th>
                <td>途中退会を行った場合、商品の性質上、返金は受け付けないものとします。</td>
              </tr>
              <tr>
                <th>返品/返金の可否</th>
                <td>提供サービスの特性上、一切の返品/返金はお受けできかねます。</td>
              </tr>
              <tr>
                <th>商品販売価格</th>
                <td>月額50,000円に加えて、マッチング実績人数に応じた成果報酬が発生</td>
              </tr>
            </tbody>
          </table>
          <div class="text-center">
            <button class="btn deet-btn" @click="$router.go(-1)">戻る</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
  h2 {
    color: #FFF;
  }
  table {
    th,
    td {
      color: #FFF;
      background-color: transparent;
      border: none;
      padding-top: 1.5rem;
      padding-bottom: 1.5rem;
    }
  }
</style>