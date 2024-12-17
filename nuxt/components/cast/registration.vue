<template>
	<div>
		<div
			class="d-flex justify-content-between align-items-center content-header"
		>
			<h4>女性会員登録</h4>
		</div>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<h4>登録</h4>
				</div>
				<div class="card-body">
					<div class="register-area">
						<form @submit.prevent="submitForm">
							<!-- Name in Japanese -->
							<div class="form-group">
								<label for="name_ja">Name (Japanese)</label>
								<input
									type="text"
									v-model="form.name_ja"
									id="name_ja"
									required
									placeholder="Enter name in Japanese"
									class="form-control"
								/>
							</div>

							<!-- Name in Kana -->
							<div class="form-group">
								<label for="name_kana">Name (Kana)</label>
								<input
									type="text"
									v-model="form.name_kana"
									id="name_kana"
									required
									placeholder="Enter name in Kana"
									class="form-control"
								/>
							</div>

							<!-- City -->
							<div class="form-group">
								<label for="city">City</label>
								<input
									type="text"
									v-model="form.city"
									id="city"
									required
									placeholder="Enter city"
									class="form-control"
								/>
							</div>

							<!-- Introducer ID -->
							<div class="form-group">
								<label for="introducer_id">Introducer ID</label>
								<input
									type="number"
									v-model="form.introducer_id"
									id="introducer_id"
									placeholder="Enter introducer ID"
									class="form-control"
								/>
							</div>

							<!-- Person in Charge -->
							<div class="form-group">
								<label for="person_in_charge_id">Person in Charge</label>
								<input
									type="number"
									v-model="form.person_in_charge_id"
									id="person_in_charge_id"
									required
									placeholder="Enter person in charge ID"
									class="form-control"
								/>
							</div>

							<!-- Registered Date -->
							<div class="form-group">
								<label for="registered_date">Registered Date</label>
								<input
									type="date"
									v-model="form.registered_date"
									id="registered_date"
									required
									class="form-control"
								/>
							</div>

							<!-- Status ID -->
							<div class="form-group">
								<label for="status_id">Status</label>
								<input
									type="number"
									v-model="form.status_id"
									id="status_id"
									required
									placeholder="Enter status ID"
									class="form-control"
								/>
							</div>

							<!-- Rank -->
							<div class="form-group">
								<label for="rank">Rank</label>
								<input
									type="text"
									v-model="form.rank"
									id="rank"
									required
									placeholder="Enter rank"
									class="form-control"
								/>
							</div>

							<!-- Post Code -->
							<div class="form-group">
								<label for="post_code">Post Code</label>
								<input
									type="text"
									v-model="form.post_code"
									id="post_code"
									maxlength="7"
									placeholder="Enter post code"
									class="form-control"
								/>
							</div>

							<!-- Prefecture -->
							<div class="form-group">
								<label for="prefecture">Prefecture</label>
								<input
									type="text"
									v-model="form.prefecture"
									id="prefecture"
									placeholder="Enter prefecture"
									class="form-control"
								/>
							</div>

							<!-- Municipality -->
							<div class="form-group">
								<label for="municipalitie">Municipality</label>
								<input
									type="text"
									v-model="form.municipalitie"
									id="municipalitie"
									placeholder="Enter municipality"
									class="form-control"
								/>
							</div>

							<!-- Street -->
							<div class="form-group">
								<label for="street">Street</label>
								<input
									type="text"
									v-model="form.street"
									id="street"
									placeholder="Enter street"
									class="form-control"
								/>
							</div>

							<!-- Building -->
							<div class="form-group">
								<label for="building">Building</label>
								<input
									type="text"
									v-model="form.building"
									id="building"
									placeholder="Enter building"
									class="form-control"
								/>
							</div>

							<!-- Station -->
							<div class="form-group">
								<label for="station">Station</label>
								<input
									type="text"
									v-model="form.station"
									id="station"
									placeholder="Enter station"
									class="form-control"
								/>
							</div>

							<!-- Footwork -->
							<div class="form-group">
								<label for="footwork">Footwork</label>
								<select
									v-model="form.footwork"
									id="footwork"
									required
									class="form-control"
								>
									<option disabled value="">Select Footwork</option>
									<option value="good">Good</option>
									<option value="average">Average</option>
									<option value="bad">Bad</option>
								</select>
							</div>

							<!-- Alcohol Tolerance -->
							<div class="form-group">
								<label for="alcohol">Alcohol Tolerance</label>
								<select
									v-model="form.alcohol"
									id="alcohol"
									required
									class="form-control"
								>
									<option disabled value="">Select Alcohol Tolerance</option>
									<option value="good">Good</option>
									<option value="average">Average</option>
									<option value="bad">Bad</option>
									<option value="not">Not</option>
								</select>
							</div>

							<!-- Day Work -->
							<div class="form-group">
								<label for="day_work">Day Work</label>
								<input
									type="text"
									v-model="form.day_work"
									id="day_work"
									placeholder="Enter day work"
									class="form-control"
								/>
							</div>

							<!-- Night Work -->
							<div class="form-group">
								<label for="night_work">Night Work</label>
								<input
									type="text"
									v-model="form.night_work"
									id="night_work"
									placeholder="Enter night work"
									class="form-control"
								/>
							</div>

							<!-- Height -->
							<div class="form-group">
								<label for="height">Height (cm)</label>
								<input
									type="number"
									v-model="form.height"
									id="height"
									placeholder="Enter height"
									class="form-control"
								/>
							</div>

							<!-- Three Size Bust -->
							<div class="form-group">
								<label for="three_size_b">Three Size (Bust)</label>
								<input
									type="number"
									v-model="form.three_size_b"
									id="three_size_b"
									placeholder="Enter bust size"
									class="form-control"
								/>
							</div>

							<!-- Three Size Waist -->
							<div class="form-group">
								<label for="three_size_w">Three Size (Waist)</label>
								<input
									type="number"
									v-model="form.three_size_w"
									id="three_size_w"
									placeholder="Enter waist size"
									class="form-control"
								/>
							</div>

							<!-- Three Size Hips -->
							<div class="form-group">
								<label for="three_size_h">Three Size (Hips)</label>
								<input
									type="number"
									v-model="form.three_size_h"
									id="three_size_h"
									placeholder="Enter hips size"
									class="form-control"
								/>
							</div>

							<!-- VIP Status -->
							<div class="form-group">
								<label for="vip_status">VIP Status</label>
								<select
									v-model="form.vip_status"
									id="vip_status"
									required
									class="form-control"
								>
									<option disabled value="">Select VIP Status</option>
									<option value="good">Good</option>
									<option value="average">Average</option>
									<option value="bad">Bad</option>
								</select>
							</div>

							<!-- Birthday -->
							<div class="form-group">
								<label for="birthday">Birthday</label>
								<input
									type="date"
									v-model="form.birthday"
									id="birthday"
									class="form-control"
								/>
							</div>

							<!-- CEO Check -->
							<div class="form-group">
								<label for="ceo_check">CEO Check</label>
								<input
									type="checkbox"
									v-model="form.ceo_check"
									id="ceo_check"
								/>
							</div>

							<!-- Instagram ID -->
							<div class="form-group">
								<label for="instagram_id">Instagram ID</label>
								<input
									type="text"
									v-model="form.instagram_id"
									id="instagram_id"
									placeholder="Enter Instagram ID"
									class="form-control"
								/>
							</div>

							<!-- Category ID -->
							<div class="form-group">
								<label for="category_id">Category ID</label>
								<input
									type="number"
									v-model="form.category_id"
									id="category_id"
									required
									placeholder="Enter category ID"
									class="form-control"
								/>
							</div>

							<!-- Tag of Spec -->
							<div class="form-group">
								<label for="tag_of_spec">Tag of Spec</label>
								<textarea
									v-model="form.tag_of_spec"
									id="tag_of_spec"
									placeholder="Enter tag of spec"
									class="form-control"
								></textarea>
							</div>

							<!-- Notices -->
							<div class="form-group">
								<label for="notices">Notices</label>
								<textarea
									v-model="form.notices"
									id="notices"
									placeholder="Enter notices"
									class="form-control"
								></textarea>
							</div>

							<button type="submit" class="btn btn-success register-btn">
								Register Cast
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div v-if="castStore.errorMessage" class="error-message">
			{{ castStore.errorMessage }}
		</div>
		<div v-if="castStore.successMessage" class="success-message">
			{{ castStore.successMessage }}
		</div>
	</div>
</template>

<script setup>
import { ref } from 'vue';
import { useCastStore } from '../../stores/casts.js';

const castStore = useCastStore();

const form = ref({
	name_ja: '',
	name_kana: '',
	city: '',
	introducer_id: null,
	person_in_charge_id: null,
	registered_date: '',
	status_id: null,
	rank: '',
	post_code: '',
	prefecture: '',
	municipalitie: '',
	street: '',
	building: '',
	station: '',
	footwork: '',
	alcohol: '',
	day_work: '',
	night_work: '',
	height: null,
	three_size_b: null,
	three_size_w: null,
	three_size_h: null,
	vip_status: '',
	birthday: '',
	ceo_check: false,
	instagram_id: '',
	category_id: null,
	tag_of_spec: '',
	notices: '',
});

const submitForm = async () => {
	await castStore.registerCast(form.value);
};
</script>

<style scoped>
.container {
	margin-top: 44px;
	max-width: 900px;
}
.cast-registration {
	max-width: 400px;
	margin: 0 auto;
	padding: 20px;
}

.form-group {
	margin-bottom: 20px;
}

.form-group label {
	display: block;
	margin-bottom: 5px;
}

.form-group input,
.form-group select,
.form-group textarea {
	width: 100%;
	padding: 8px;
	box-sizing: border-box;
}

button {
	padding: 10px 20px;
	background-color: #007bff;
	color: white;
	border: none;
	cursor: pointer;
}

button:hover {
	background-color: #0056b3;
}

.error-message {
	color: red;
	margin-top: 20px;
}

.success-message {
	color: green;
	margin-top: 20px;
}

.content-header {
	padding: 11px 24px;
	background: #ffffff 0% 0% no-repeat padding-box;
}
</style>
