<template>
	<Content :class="{'icon-loading': loading}" app-name="mopidy">
		<AppNavigation>
			<template id="mopidy-navigation" #list>
				<div style="margin-bottom: 5em">
					<AppNavigationItem title="TuneIn (Local Radio)"
						icon="icon-folder"
						:allow-collapse="true">
						<template>
							<AppNavigationItem v-if="!libraryTunein" :loading="!libraryTunein" title="Loading..." />
							<AppNavigationItem v-if="tuneinDirectoryStack.length > 0"
								icon="icon-view-previous"
								title="Back"
								@click="backDirectory" />
							<AppNavigationItem v-for="lib in libraryTunein"
								:key="lib.name"
								:icon="getTrackIcon(lib)"
								:title="lib.name"
								@click="pickTrack(lib)" />
						</template>
					</AppNavigationItem>
				</div>
			</template>
		</AppNavigation>
		<AppContent>
			<div>
				<div class="vertical">
					<div v-if="trackDetail">
						<h2 v-text="streamTitle" />
						<div style="text-align: center">
							<img v-if="trackDetail && trackDetail.album" :src="trackDetail.album.images[0] ||''" style="width: 100%">
						</div>
						<div v-if="trackDetail && trackDetail.name" style="text-align: center">
							<span>Track Name:</span>
							<strong v-text="trackDetail.name" />
						</div>
						<div v-if="trackDetail && trackDetail.genre" style="text-align: center">
							<span>Genre:</span>
							<strong v-text="trackDetail.genre" />
						</div>
					</div>
					<div class="slidecontainer">
						<input type="range"
							min="0"
							max="100"
							:value="playerVolume"
							@change="changeVolume">
						<div style="text-align: center; margin-top: -20px;"><label>Volume</label></div>
					</div>
				</div>
			</div>
			<div>
				<button v-if="playerState !== 'paused'" :disabled="playerState === 'playing'" @click="playPlayback()">Play</button>
				<button v-if="playerState === 'paused'" :disabled="playerState === 'playing'" @click="resumePlayback()">Resume</button>
				<button :disabled="playerState === 'paused'" @click="pausePlayback()">Pause</button>
				<button :disabled="playerState === 'stopped'" @click="stopPlayback()">Stop</button>
				<span class="timePos" v-text="getFriendlyPlayerTime()" />
			</div>
			<div>
				<div>
					<label>Bluetooth</label><br />
					<button class="bt icon-screen svg" @click="btConnect" />
					<button class="bt icon-screen-off svg" @click="btDisconnect" />
				</div>
			</div>
		</AppContent>
	</Content>
</template>

<script>
import Content from '@nextcloud/vue/dist/Components/Content'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'

import { generateUrl } from '@nextcloud/router'

import Mopidy from 'mopidy'

const mopidy = new Mopidy({ webSocketUrl: 'wss://nextcloudpi/mopidy-ws/' })

export default {
	name: 'App',
	components: {
		Content,
		AppContent,
		AppNavigation,
		AppNavigationItem,
	},

	data() {
		return {
			loading: false,
			libraryTunein: undefined,
			tuneinDirectoryStack: [],
			trackDetail: 'No info yet',
			streamTitle: '',
			playerState: '',
			playerVolume: 0,
			playerTime: 0,
			timer: undefined,
		}
	},
	created() {

	},
	mounted() {
		mopidy.on('state:online', this.loadData.bind(this))
		// mopidy.on('event:playbackStateChanged', this.updatePlaybackState.bind(this))
		// mopidy.on('event:trackPlaybackStarted', this.onTrackStarted.bind(this))
		// mopidy.on('event:streamTitleChanged', this.onStreamTitleChanged.bind(this))
		// mopidy.on('websocket:outgoingMessage', x => console.log('OUTGOING', x))
		// mopidy.on('websocket:incomingMessage', x => console.log('INCOME', x))

		setInterval(() => this.fetchTrackInfo(), 5000)
	},
	methods: {
		async loadData() {
			if (this.tuneinDirectoryStack.length > 0) {
				this.libraryTunein = await mopidy.library.browse({ uri: this.tuneinDirectoryStack[this.tuneinDirectoryStack.length - 1].uri })
			} else {
				this.libraryTunein = await mopidy.library.browse({ uri: 'tunein:root' })
			}
			console.log(this.libraryTunein)

			this.fetchTrackInfo()
			this.fetchPlayerState(1)
			this.playerState = await mopidy.playback.getState()
			this.playerVolume = await mopidy.mixer.getVolume()
		},
		async changeVolume(e) {
			await mopidy.mixer.setVolume({ volume: parseInt(e.target.value, 10) })
			this.playerVolume = await mopidy.mixer.getVolume()
		},
		onTrackStarted(newState) {
			console.log('#### onTrackStarted ###', newState)
		},
		async fetchTrackInfo() {
			this.trackDetail = await mopidy.playback.getCurrentTrack()
			this.streamTitle = await mopidy.playback.getStreamTitle()
			this.playerTime = (await mopidy.playback.getTimePosition() / 1000) || 0
		},
		fetchPlayerState(delay) {
			setTimeout(async() => {
				this.playerState = await mopidy.playback.getState()
				if (this.playerState === 'playing') {
					this.enableTimer()
				} else {
					this.disableTimer()
				}
			}, delay || 2000)
		},
		disableTimer() {
			clearInterval(this.timer)
		},
		enableTimer() {
			this.disableTimer()
			this.timer = setInterval(() => {
				this.playerTime++
			}, 1000)
		},
		getFriendlyPlayerTime() {
			const m = Math.floor(this.playerTime / 60).toString()
			const s = Math.floor((this.playerTime / 60 - m) * 60).toString().padStart(2, '0')
			return m + ':' + s
		},
		onStreamTitleChanged(title) {
			console.log(title)
			this.info = title
		},
		async playPlayback() {
			await mopidy.playback.play()
			this.fetchPlayerState()
		},
		async pausePlayback() {
			await mopidy.playback.pause()
			this.fetchPlayerState()
		},
		async resumePlayback() {
			await mopidy.playback.resume()
			this.fetchPlayerState()
		},
		async stopPlayback() {
			await mopidy.playback.stop()
			this.fetchPlayerState()
		},
		async backDirectory() {
			this.tuneinDirectoryStack.pop()
			await this.loadData()
		},
		async pickTrack(item) {
			if (item.type === 'directory') {
				this.tuneinDirectoryStack.push(item)
				await this.loadData()
			} else {
				await mopidy.tracklist.clear()
				await mopidy.tracklist.add({ uris: [item.uri] })
				await mopidy.playback.play({ tlid: 1 })
				this.fetchPlayerState()
			}
		},
		async btConnect() {
			const url = generateUrl('/apps/mopidyplayer/connect')
			fetch(url, {
				method: 'POST', // or 'PUT'
				headers: {
					'Content-Type': 'application/json',
				},
			})
				.then(response => response.json())
				.then(data => {
					console.log('Success:', data)
				})
				.catch((error) => {
					console.error('Error:', error)
				})
		},
		async btDisconnect() {
			const url = generateUrl('/apps/mopidyplayer/disconnect')
			fetch(url, {
				method: 'POST', // or 'PUT'
				headers: {
					'Content-Type': 'application/json',
				},
			})
				.then(response => response.json())
				.then(data => {
					console.log('Success:', data)
				})
				.catch((error) => {
					console.error('Error:', error)
				})
		},
		getTrackIcon(item) {
			if (item.type === 'directory') {
				return 'icon-category-organization'
			} else {
				return 'icon-music'
			}
		},
		addOption(val) {
			this.options.push(val)
			this.select.push(val)
		},
		previous(data) {
			console.debug(data)
		},
		next(data) {
			console.debug(data)
		},
		close(data) {
			console.debug(data)
		},
		newButtonAction(e) {
			console.debug(e)
		},
		log(e) {
			console.debug(e)
		},

	},
}
</script>
