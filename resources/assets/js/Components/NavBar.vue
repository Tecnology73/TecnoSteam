<template>
	<div class="ts-nav-bar">
		<div class="container">
			<div class="brand">TecnoSteam</div>

			<div class="nav-group">
				<a href="/" class="nav-item" :class="{ 'active': isActive('/') }">Home</a>
				<a href="/forums" class="nav-item" :class="{ 'active': isActive('/forums') }">Forums</a>
			</div>

			<div class="user-info logged-in" v-if="user">
				{{ user.nickname }}
			</div>

			<div class="user-info" v-else>
				<a href="/login" class="login-button">
					Login with Steam
				</a>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props   : {
			user: {
				type   : Object,
				default: null,
			},
		},
		computed: {
			isLoggedIn() {
				return (this.user === null);
			},
		},
		methods : {
			isActive(route) {
				return window.location.pathname === route;
			},
		},
	}
</script>

<style scoped lang="scss">
	@import '../../scss/variables';

	.ts-nav-bar {
		height: 50px;
		line-height: 50px;
		background-color: darken($theme-primary, 5%);
		color: $theme-primary-inverse;
		box-shadow: 0 2px 3px rgba(0, 0, 0, 0.5);
		z-index: 9999;

		> .container {
			display: flex;
			align-items: stretch;
		}

		.brand {
			flex: 0 0 auto;
			margin-right: 1em;
			font-size: 1.5em;
			font-weight: bold;
		}

		.nav-group {
			flex: 1 1 100%;
			display: flex;

			.nav-item {
				flex: 0 0 auto;
				padding: 0 1em;
				cursor: pointer;
				transition: background-color ease-in-out 0.1s;

				&.active {
					font-weight: bold;
					color: $theme-accent;
				}

				&:hover {
					background-color: rgba(255, 255, 255, 0.05);
				}

				&:active {
					background-color: rgba(255, 255, 255, 0.1);
				}
			}
		}

		.user-info {
			flex: 0 0 auto;
			margin-right: -1em;

			&.logged-in {
				padding: 0 1em;
			}

			.login-button {
				display: block;
				padding: 0 1em;
				cursor: pointer;
				transition: background-color ease-in-out 0.1s;

				&:hover {
					background-color: rgba(255, 255, 255, 0.05);
				}

				&:active {
					background-color: rgba(255, 255, 255, 0.1);
				}
			}
		}
	}
</style>
