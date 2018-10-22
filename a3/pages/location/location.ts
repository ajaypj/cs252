import { Component } from '@angular/core';
import { IonicPage,  NavParams } from 'ionic-angular';
import { ViewController } from 'ionic-angular';
// import { GoogleMaps, GoogleMap, Environment } from '@ionic-native/google-maps';
import leaflet from 'leaflet';
leaflet.Icon.Default.imagePath = 'images/';

@IonicPage()
@Component({
  selector: 'page-location',
  templateUrl: 'location.html',
})
export class LocationPage {
  // @ViewChild('map') mapContainer : ElementRef;
  lat: number=0;
  lng: number=0;
  map: leaflet.map;
  center: leaflet.PointTuple;
  constructor(private viewCtrl: ViewController, 
  private navParams: NavParams) {
    this.lat = this.navParams.data.lat;
    this.lng = this.navParams.data.lng;
    console.log("Reached here",this.lat,this.lng)
  }
  ionViewDidLoad() {
  console.log('ionViewDidLoad LocationPage');
  this.center = [this.lat, this.lng];
  this.loadMap();
}
  loadMap() {
    this.map = leaflet.map("mapId",{center: this.center, zoom: 16});
    
    var position = leaflet.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                                attribution: 'edupala.com © ionic LeafLet'}).addTo(this.map);
    var marker = new leaflet.marker([this.lat, this.lng]).addTo(this.map);
    console.log("MAP LOADED");

  }
  onDismiss(){
    this.viewCtrl.dismiss();
  }
}
