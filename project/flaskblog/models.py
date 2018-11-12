from datetime import datetime
from flaskblog import db, login_manager
from flask_login import UserMixin


@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))


class User(db.Model, UserMixin):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(20), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    image_file = db.Column(db.String(20), nullable=False, default='default.jpg')
    password = db.Column(db.String(60), nullable=False)
    posts = db.relationship('Post', backref='author', lazy=True)
    transactions = db.relationship('Transaction', backref='Contributor', lazy=True)
    no_of_succesful_transactions = db.Column(db.Integer,default = 0)
    total_contribution = db.Column(db.Integer,default = 0)
    def __repr__(self):
        return f"User('{self.username}', '{self.email}', '{self.image_file}')"


class Post(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(100), nullable=False)
    date_posted = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    content = db.Column(db.Text, nullable=False)
    amount_requested = db.Column(db.Integer,nullable=False)
    amount_collected = db.Column(db.Integer,nullable=False,default=0)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    charge_id = db.Column(db.String(40),nullable=False)
    transactions = db.relationship('Transaction', backref='Reciever', lazy=True)
    status = db.Column(db.String(20),nullable=False,default="running")
    remaining_time = db.Column(db.Integer,nullable=False)
    trendiness = db.Column(db.Integer,nullable=False,default=0)
    def __repr__(self):
        return f"Post('{self.title}', '{self.date_posted}')"


class Transaction(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    Amount = db.Column(db.Integer, nullable=False)
    transaction_id = db.Column(db.String(20),nullable=False,unique=True)
    date_of_Transaction = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    post_id = db.Column(db.Integer, db.ForeignKey('post.id'), nullable=False)
    transaction_type = db.Column(db.String(20),nullable=False,default="donation")
