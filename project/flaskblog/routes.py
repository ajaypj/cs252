import os
import secrets
import decimal
from PIL import Image
from flask import render_template, url_for, flash, redirect, request, abort
from flaskblog import app, db, bcrypt
from flaskblog.forms import RegistrationForm, LoginForm, UpdateAccountForm, PostForm
from flaskblog.models import User, Post, Transaction
from flask_login import login_user, current_user, logout_user, login_required
from apscheduler.scheduler import Scheduler
import stripe

@app.route("/")
@app.route("/home")
def home():
    posts = Post.query.filter_by(status="running").all()
    posts.reverse()
    return render_template('home.html', posts=posts)


@app.route("/about")
def about():
    return render_template('about.html', title='About')


@app.route("/register", methods=['GET', 'POST'])
def register():
    if current_user.is_authenticated:
        return redirect(url_for('home'))
    form = RegistrationForm()
    if form.validate_on_submit():
        hashed_password = bcrypt.generate_password_hash(form.password.data).decode('utf-8')
        user = User(username=form.username.data, email=form.email.data, password=hashed_password)
        db.session.add(user)
        db.session.commit()
        flash('Your account has been created! You are now able to log in', 'success')
        return redirect(url_for('login'))
    return render_template('register.html', title='Register', form=form)


@app.route("/login", methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('home'))
    form = LoginForm()
    if form.validate_on_submit():
        user = User.query.filter_by(email=form.email.data).first()
        if user and bcrypt.check_password_hash(user.password, form.password.data):
            login_user(user, remember=form.remember.data)
            next_page = request.args.get('next')
            return redirect(next_page) if next_page else redirect(url_for('home'))
        else:
            flash('Login Unsuccessful. Please check email and password', 'danger')
    return render_template('login.html', title='Login', form=form)


@app.route("/logout")
def logout():
    logout_user()
    return redirect(url_for('home'))


def save_picture(form_picture):
    random_hex = secrets.token_hex(8)
    _, f_ext = os.path.splitext(form_picture.filename)
    picture_fn = random_hex + f_ext
    picture_path = os.path.join(app.root_path, 'static/profile_pics', picture_fn)

    output_size = (125, 125)
    i = Image.open(form_picture)
    i.thumbnail(output_size)
    i.save(picture_path)

    return picture_fn


@app.route("/account", methods=['GET', 'POST'])
@login_required
def account():
    form = UpdateAccountForm()
    if form.validate_on_submit():
        if form.picture.data:
            picture_file = save_picture(form.picture.data)
            current_user.image_file = picture_file
        current_user.username = form.username.data
        current_user.email = form.email.data
        db.session.commit()
        flash('Your account has been updated!', 'success')
        return redirect(url_for('account'))
    elif request.method == 'GET':
        form.username.data = current_user.username
        form.email.data = current_user.email
    image_file = url_for('static', filename='profile_pics/' + current_user.image_file)
    return render_template('account.html', title='Account',
                           image_file=image_file, form=form,user=current_user)


@app.route("/post/new", methods=['GET', 'POST'])
@login_required
def new_post():
	form = PostForm()
	if form.validate_on_submit():
		return render_template('pay_post_fees.html',form=request.form)
	return render_template('create_post.html', title='New Post',
                           form=form, legend='New Post')
@app.route("/paid_fee",methods=['GET', 'POST'])
@login_required
def paid_fee():
	stripe.api_key = "sk_test_Ee67bjNid7V4TZC17DKtgQYE"
	token = request.form['stripeToken']
	charge = stripe.Charge.create( amount="50000",currency='inr',description='Donation charge',source=token,capture=False)
	if(charge['status'] == "succeeded" ):
		post = Post(title=request.form['title'], content=request.form['content'], author=current_user, amount_requested = request.form['amount_requested'] , remaining_time = int(request.form['days'])*24*60+int(request.form['hours'])*60+int(request.form['minutes']),charge_id = charge['id'])
		db.session.add(post)
		db.session.commit()
		flash('Your post has been created!', 'success')
		current_post = post
		transaction = Transaction(Amount = 500 , transaction_id = charge['id'] ,Contributor = current_user, Reciever = current_post,transaction_type="PostingFee")
		db.session.add(transaction)
		db.session.commit()
	else :
		flash('Payment Failed','danger')
	return redirect(url_for('home'))

@app.route("/pay",methods=['POST'])
@login_required
def pay():
	return render_template('pay.html',amount=int(request.form['amount']),post_id = request.form['post_id'])
@app.route("/payment",methods=['POST'])
def payment():
	stripe.api_key = "sk_test_Ee67bjNid7V4TZC17DKtgQYE"
	token = request.form['stripeToken']
	charge = stripe.Charge.create( amount=int(request.form['amount'])*100,currency='inr',description='Donation charge',source=token,capture=False)
	user = User.query.get(current_user.id)
	if(charge['status'] == "succeeded" ):
		current_post = Post.query.get(request.form['post_id'] )
		transaction = Transaction(Amount = int(charge['amount'])/100 , transaction_id = charge['id'] ,Contributor = current_user, Reciever = current_post)
		current_post.amount_collected += int(request.form['amount'])
		current_post.amount_requested -= int(request.form['amount'])
		db.session.add(transaction)
		db.session.commit()
		if current_post.amount_requested <= 0:
			charge2 = stripe.Refund.create(charge = current_post.charge_id
				);
			transaction2 = Transaction(Amount = current_post.amount_collected,transaction_id = charge2['id'],Contributor = current_post.author,Reciever = current_post,transaction_type="RecivedPay")
			db.session.add(transaction2)
			db.session.commit()
			current_post.trendiness=0
			current_post.status = "completed"
		current_user.no_of_succesful_transactions += 1
		current_user.total_contribution += int(charge['amount'])
		db.session.commit()
		flash('Your Payment is completed! Thank You For Your contribution to this community!', 'success')
	else :
		flash('Payment Failed','danger')
	return redirect(url_for('home'))

@app.route("/user/<int:user_id>",methods = [ 'GET', 'POST' ] )
def user(user_id):
    user = User.query.get_or_404(user_id)
    form = UpdateAccountForm()
    form.username.data = user.username
    form.email.data = user.email
    image_file = url_for('static', filename='profile_pics/' + user.image_file)
    return render_template('account.html', title='Account',
                           image_file=image_file, form=form,user=user)
@app.route("/post/<int:post_id>")
def post(post_id):
    post = Post.query.get_or_404(post_id)
    if(post.status=="running"):
         post.trendiness+=10000
    db.session.commit()
    print("call"+str(post_id))
    return render_template('post.html', title=post.title, post=post)


@app.route("/post/<int:post_id>/update", methods=['GET', 'POST'])
@login_required
def update_post(post_id):
    post = Post.query.get_or_404(post_id)
    if post.author != current_user:
        abort(403)
    form = PostForm()
    if form.validate_on_submit():
        post.title = form.title.data
        post.content = form.content.data
        db.session.commit()
        flash('Your post has been updated!', 'success')
        return redirect(url_for('post', post_id=post.id))
    elif request.method == 'GET':
        form.title.data = post.title
        form.content.data = post.content
    return render_template('create_post.html', title='Update Post',
                           form=form, legend='Update Post')
cron = Scheduler(daemon=True)
# Explicitly kick off the background thread
cron.start()

@cron.interval_schedule(minutes=1)
def job_function():
	posts = Post.query.filter_by(status="running").all()
	for post in posts:
		post.trendiness*=0.83
		post.remaining_time -= 1
		db.session.commit()
		if(post.remaining_time == 0):
			post.trendiness=0
			post.status = "failed" 
			db.session.commit()
			for transaction in post.transactions:
				if(transaction.transaction_type=="refund") :
					continue
				stripe.api_key = "sk_test_Ee67bjNid7V4TZC17DKtgQYE"
				refund = stripe.Refund.create(
						    charge=transaction.transaction_id,
							)
				user = User.query.get(transaction.user_id)
				transaction2 = Transaction(Amount = transaction.Amount , transaction_id = refund['id'] ,Contributor =  user , Reciever = post, transaction_type = "refund" )
				db.session.add(transaction2)
				db.session.commit()
@app.route("/post/<int:post_id>/delete", methods=['POST'])
@login_required
def delete_post(post_id):
	post = Post.query.get_or_404(post_id)
	if post.author != current_user:
		abort(403)
	for transaction in post.transactions:
		if(transaction.transaction_type!="donation") :
			continue
		stripe.api_key = "sk_test_Ee67bjNid7V4TZC17DKtgQYE"
		print(transaction.transaction_id)
		refund = stripe.Refund.create(
					charge=transaction.transaction_id,
					)
		user = User.query.get(transaction.user_id)
		transaction2 = Transaction(Amount = transaction.Amount , transaction_id = refund['id'] ,Contributor =  user , Reciever = post, transaction_type = "postDeleted" )
		db.session.add(transaction2)
	post.trendiness=(0)
	post.status="deleted"
	db.session.commit()
	flash('Your post has been deleted!', 'success')
	return redirect(url_for('home'))

@app.route("/my_transactions")
@login_required
def my_transactions():
	return render_template('my_transactions.html')

@app.route("/trending")
def trending():
    posts = Post.query.filter_by(status="running").order_by(Post.trendiness.desc()).all()
    return render_template('home.html', posts=posts)

@app.route("/Failed_Requests")
def Failed_Requests():
    posts = Post.query.filter_by(status="failed").all()
    return render_template('home.html', posts=posts)

@app.route("/Completed_Requests")
def Completed_Requests():
    posts = Post.query.filter_by(status="completed").all()
    return render_template('home.html', posts=posts)

@app.route("/topdonor")
def topDonor():
    donors = User.query.order_by(User.total_contribution.desc()).all()
    return render_template('topdonors.html', donors=donors)
