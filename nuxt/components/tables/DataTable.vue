<template>
	<section class="container mt-4">
		<div class="card mb-4 shadow-sm">
			<div class="card-header bg-dark">
				<h5 class="text-light">カテゴリー</h5>
			</div>
			<table class="table table-hover table-striped mb-0">
				<thead class="thead-light">
					<tr>
						<th scope="col" class="px-4 py-3">カテゴリ名</th>
						<th scope="col" class="px-4 py-3 text-center">編集</th>
						<th scope="col" class="px-4 py-3 text-center">消去</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row" class="px-4 py-3">カテゴリ名</th>
						<td class="px-4 py-3 text-center">
							<button type="submit" class="btn btn-success text-light">
								編集
							</button>
						</td>
						<td class="px-4 py-3 text-center">
							<button type="submit" class="btn btn-danger text-light">
								消去
							</button>
						</td>
					</tr>
					<tr>
						<th scope="row" class="px-4 py-3">カテゴリ名</th>
						<td class="px-4 py-3 text-center">
							<button type="submit" class="btn btn-success text-light">
								編集
							</button>
						</td>
						<td class="px-4 py-3 text-center">
							<button type="submit" class="btn btn-danger text-light">
								消去
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
</template>
