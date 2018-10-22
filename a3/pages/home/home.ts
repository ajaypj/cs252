import { Component } from '@angular/core';
import { ModalController, NavController } from 'ionic-angular';
import { Geolocation } from '@ionic-native/geolocation';
import { LocationPage } from '../location/location';
@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage { 
  location: {lat: number, lng: number} = {lat:0, lng:0} ;
  constructor(
    public navCtrl: NavController,
    private geolocation: Geolocation,
    private modalCtrl:ModalController
    ) {

  }
  onLocateUser() {
    this.geolocation.getCurrentPosition().then((resp) => {
  // resp.coords.latitude
  // resp.coords.longitude
  console.log('Location fetched successfully');
  this.location.lat=resp.coords.latitude;
  this.location.lng=resp.coords.longitude;
  }).catch((error) => {
    console.log('Error getting location', error);
  });
  }
  
  openMap(){
    console.log(this.location);
    console.log(location);
    this.modalCtrl.create(LocationPage,this.location).present();
  }
}
