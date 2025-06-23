<form action="{{ route('import') }}" method="post" enctype="multipart/form-data">
    @csrf
    <label for="file">Choose File:</label>
    <input type="file" name="file" id="file" required>
    <button type="submit">Import</button>
</form>
