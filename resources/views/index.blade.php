@extends('main')
@section('content')
    @foreach ($errors->all() as $error)
        <li class="alert alert-danger">{{ $error }}</li>
    @endforeach
    <form method="POST" action="{{ route('store') }}">
        @csrf
        @foreach($productsArray as $product)
            <div class="col-md-4">
                <h2>{{ $product->name }}</h2>
                <img style="width:300px" alt="Тут должно быть фото" src={{ $product->imageUrl }} />
            </div>
                <input type="checkbox" class="form-check-input" id="exampleCheck1" name={{ $product->offers[0]->id }}>
                <label class="form-check-label" for="exampleCheck1">выбрать</label>
        @endforeach
        <br>
        <div class="mb-3">
            <label for="exampleInputText" class="form-label">Name</label>
            <input type="text" class="form-control" id="exampleInputText" name="name">
        </div>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email</label>
            <input type="email" class="form-control" id="exampleInputEmail1" name="email">
        </div>
        <div class="mb-3">
            <label for="exampleInputPhone" class="form-label">Phone</label>
            <input type="tel" class="form-control" id="exampleInputPhone" name="phone">
        </div>
        <button type="submit" class="btn btn-primary">Оформить заказ</button>
    </form>
@endsection

